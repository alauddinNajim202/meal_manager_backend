<?php
namespace App\Http\Controllers\Api\Frontend;

use App\Http\Controllers\Controller;
use App\Models\ChatMessage;
use App\Models\ChatSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Nette\Utils\Random;
use App\Models\CryptoStore;

class AiChatBotController extends Controller
{
    private string $webhookUrl;
    private int $timeout;

    public function __construct()
    {
        parent::__construct();
        $this->webhookUrl = config('services.chatbot.webhook_url');
        $this->timeout    = config('services.chatbot.timeout', 30);
    }

    public function chatbotMessage(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'question'   => ['required', 'string', 'min:3', 'max:1000'],
            'language'   => ['required', 'string', 'in:nl,en'],
            'session_id' => ['required', 'integer', 'exists:chat_sessions,id'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();
        $userId    = auth('api')->id();

        // ── Check limit ──────────────────────────────────────────────
        $setting = \App\Models\Setting::first();
        $limit = $setting->chatbot_question_limit ?? 50;

        $questionCount = \App\Models\ChatMessage::where('role', 'user')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->whereHas('session', function($q) use ($userId) {
                $q->where('user_id', $userId);
            })->count();

        if ($questionCount >= $limit) {
            return response()->json([
                'success' => false,
                'message' => 'You have reached your monthly question limit.',
                'outcome' => 'error',
                'reason_code' => 'limit_reached',
            ], 403);
        }

        // ── Session: continue  ───────────────────────────────────
        if (! empty($validated['session_id'])) {
            // existing session continue
            $session = ChatSession::where('id', $validated['session_id'])
                ->where('user_id', $userId)
                ->firstOrFail();
        } else {
            // create new session
            $session = ChatSession::create([
                'user_id'  => $userId,
                'language' => $validated['language'],
                'title'    => substr($validated['question'], 0, 60),
            ]);
        }

        // ── 2. Save user question ─────────────────────────────────────────────
        ChatMessage::create([
            'session_id' => $session->id,
            'role'       => 'user',
            'message'    => $validated['question'],
        ]);

        // ── 3. Call n8n webhook ───────────────────────────────────────────────
        try {
            $response = Http::timeout($this->timeout)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept'       => 'application/json',
                ])
                ->get($this->webhookUrl, [
                    'question'   => $validated['question'],
                    'language'   => $validated['language'],
                    'user_id'    => Hash::make($userId),
                    'session_id' => Random::generate(16) . "_" . $session->id,
                ]);

        } catch (\Exception $e) {
            Log::error('Chatbot connection error', ['error' => $e->getMessage()]);

            return response()->json([
                'success'     => false,
                'answer'      => 'Could not connect to the chatbot service.',
                'outcome'     => 'error',
                'reason_code' => 'connection_error',
            ], 503);
        }

        // ── 4. Handle HTTP error ──────────────────────────────────────────────
        if ($response->failed()) {
            Log::error('Chatbot webhook failed', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);

            return response()->json([
                'success'     => false,
                'answer'      => 'The chatbot service is temporarily unavailable.',
                'outcome'     => 'error',
                'reason_code' => 'webhook_error',
            ], 503);
        }

        // ── 5. Parse response ─────────────────────────────────────────────────
        $data = $response->json();

        // n8n array response handle করা
        if (is_array($data) && isset($data[0])) {
            $data = $data[0];
        }

        if (empty($data['answer'])) {
            Log::warning('Chatbot returned empty answer', ['data' => $data]);

            return response()->json([
                'success'     => false,
                'answer'      => 'No answer received. Please try again.',
                'outcome'     => 'error',
                'reason_code' => 'empty_response',
            ], 502);
        }

        // ── 6. Save bot answer ────────────────────────────────────────────────
        $messages =  ChatMessage::create([
            'session_id'  => $session->id,
            'role'        => 'bot',
            'message'     => $data['answer'],
            'outcome'     => $data['outcome'] ?? 'unknown',
            'reason_code' => $data['reason_code'] ?? null,
            'category'    => $data['category'] ?? null,
            'crypstore_url' => $data['crypstore_url'] ?? null,
        ]);

        $perPage = $request->get('per_page', 20);



        return response()->json([
            'message' => 'Chat conversation retrieved successfully',
            'success' => true,
            'data'    => [
                'session_id' => $session->id,
                'messages'   => $messages,
                // 'max_limit' =>  $limit,
                // 'question_count' => $questionCount,

            ],
            'code'    => 200,
        ]);
    }

    // Chat history দেখার জন্য
    public function chatbotConversation(Request $request)
    {
        $userId  = auth('api')->id();
        $perPage = $request->get('per_page', 20);

        $session = ChatSession::firstOrCreate(
            ['user_id' => $userId],
            ['language' => $request->get('language', 'en')]
        );

        $messages = ChatMessage::where('session_id', $session->id)
            ->orderBy('created_at', 'asc')
            ->paginate($perPage);

        $setting = \App\Models\Setting::first();
        $limit = $setting->chatbot_question_limit ?? 50;

        $questionCount = \App\Models\ChatMessage::where('role', 'user')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->whereHas('session', function($q) use ($userId) {
                $q->where('user_id', $userId);
            })->count();

        $formattedMessages = collect($messages->items())->map(function ($message) {
            return [
                'id'          => $message->id,
                'session_id'  => $message->session_id,
                'role'        => $message->role,
                'message'     => $message->message,
                'outcome'     => $message->outcome,
                'reason_code' => $message->reason_code,
                'category'    => $message->category,
                'crypstore_url' => $message->crypstore_url,
                'created_at'  => $message->created_at->toDateTimeString(),
            ];
        });

        return response()->json([
            'message' => 'Chat conversation retrieved successfully',
            'success' => true,
            'data'    => [
                'session_id' => $session->id,
                'messages'   => $formattedMessages,
                'pagination' => [
                    'total_page'        => $messages->total(),
                    'per_page'     => $messages->perPage(),
                    'total_item'    => $messages->total(),
                    'current_page' => $messages->currentPage(),
                ],
                'max_limit' =>  $limit,
                'question_count' => $questionCount,
                'days_left' => now()->daysInMonth - now()->day,
            ],
            'code'    => 200,
        ]);
    }

    // list of data
     public function getCryptoStoresList(Request $request)
    {
        try {
            $stores = CryptoStore::select('name', 'slug', 'type')->get();

            // n8n directly iterates the array: s.json.name, s.json.slug, s.json.type
            return response()->json($stores);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch stores',
                'error'   => $e->getMessage()
            ], 500);
        }
    }


}
