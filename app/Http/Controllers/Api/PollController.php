<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Poll;
use App\Models\PollOption;
use App\Models\PollVote;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Exception;

class PollController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $user = auth('api')->user();
        $messId = $user->current_mess_id;

        if (!$messId) {
            return $this->error(null, 'No active mess selected.', 400);
        }

        $polls = Poll::where('mess_id', $messId)
            ->with(['options' => function($q) {
                $q->withCount('votes');
            }, 'creator:id,name,avatar'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($poll) use ($user) {
                // Check if this user voted on this poll
                $userVote = $poll->votes()->where('user_id', $user->id)->first();
                $totalVotes = $poll->votes()->count();
                
                return [
                    'id' => $poll->id,
                    'title' => $poll->title,
                    'meal_type' => $poll->meal_type,
                    'duration_hours' => $poll->duration_hours,
                    'expires_at' => $poll->expires_at,
                    'date' => $poll->date,
                    'status' => $poll->status,
                    'created_by' => $poll->creator,
                    'created_at' => $poll->created_at,
                    'total_votes' => $totalVotes,
                    'has_voted' => $userVote ? true : false,
                    'voted_option_id' => $userVote ? $userVote->poll_option_id : null,
                    'options' => $poll->options->map(function ($option) use ($totalVotes) {
                        return [
                            'id' => $option->id,
                            'text' => $option->text,
                            'votes_count' => $option->votes_count,
                            'percentage' => $totalVotes > 0 ? round(($option->votes_count / $totalVotes) * 100) : 0,
                        ];
                    }),
                ];
            });

        return $this->success($polls, 'Polls fetched successfully.', 200);
    }

    public function store(Request $request)
    {
        $user = auth('api')->user();
        $messId = $user->current_mess_id;

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'meal_type' => 'required|string|in:Breakfast,Lunch,Dinner,Both (Lunch & Dinner),Special Feast',
            'duration_hours' => 'required|integer',
            'date' => 'nullable|date',
            'options' => 'required|array|min:2|max:6',
            'options.*' => 'required|string|max:100',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors()->first(), 'Validation failed', 422);
        }

        try {
            DB::beginTransaction();

            $poll = Poll::create([
                'mess_id' => $messId,
                'created_by' => $user->id,
                'title' => $request->title,
                'meal_type' => $request->meal_type,
                'duration_hours' => $request->duration_hours,
                'expires_at' => now()->addHours($request->duration_hours),
                'date' => $request->date,
                'status' => 'active',
            ]);

            foreach ($request->options as $optionText) {
                PollOption::create([
                    'poll_id' => $poll->id,
                    'text' => $optionText,
                ]);
            }

            DB::commit();

            // Notify all members of the mess about the new poll
            $mess = \App\Models\Mess::find($messId);
            if ($mess) {
                $mess->notify(new \App\Notifications\NewPollCreatedNotification($poll->title));
            }

            return $this->success($poll->load('options'), 'Poll created successfully.', 201);
        } catch (Exception $e) {
            DB::rollBack();
            return $this->error(null, $e->getMessage(), 500);
        }
    }

    public function vote(Request $request)
    {
        $user = auth('api')->user();
        
        $validator = Validator::make($request->all(), [
            'poll_id' => 'required|exists:polls,id',
            'option_id' => 'required|exists:poll_options,id',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors()->first(), 'Validation failed', 422);
        }

        try {
            $poll = Poll::find($request->poll_id);
            
            if ($poll->mess_id != $user->current_mess_id) {
                return $this->error(null, 'You do not belong to the mess for this poll.', 403);
            }
            
            if ($poll->status !== 'active') {
                return $this->error(null, 'This poll is closed.', 400);
            }

            $option = PollOption::where('id', $request->option_id)
                ->where('poll_id', $poll->id)
                ->first();

            if (!$option) {
                return $this->error(null, 'Invalid option for this poll.', 400);
            }

            // Update or insert the vote
            PollVote::updateOrCreate(
                ['poll_id' => $poll->id, 'user_id' => $user->id],
                ['poll_option_id' => $option->id]
            );

            return $this->success(null, 'Vote cast successfully.', 200);
        } catch (Exception $e) {
            return $this->error(null, $e->getMessage(), 500);
        }
    }

    public function close(Request $request)
    {
        $user = auth('api')->user();
        
        $validator = Validator::make($request->all(), [
            'poll_id' => 'required|exists:polls,id',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors()->first(), 'Validation failed', 422);
        }

        try {
            $poll = Poll::find($request->poll_id);
            
            if ($poll->mess_id != $user->current_mess_id) {
                return $this->error(null, 'Poll not found in your mess.', 404);
            }

            $poll->update(['status' => 'closed']);

            return $this->success(null, 'Poll closed successfully.', 200);
        } catch (Exception $e) {
            return $this->error(null, $e->getMessage(), 500);
        }
    }

    public function destroy($id)
    {
        $user = auth('api')->user();
        
        try {
            $poll = Poll::find($id);
            
            if (!$poll || $poll->mess_id != $user->current_mess_id) {
                return $this->error(null, 'Poll not found.', 404);
            }

            $poll->delete();

            return $this->success(null, 'Poll deleted successfully.', 200);
        } catch (Exception $e) {
            return $this->error(null, $e->getMessage(), 500);
        }
    }
}
