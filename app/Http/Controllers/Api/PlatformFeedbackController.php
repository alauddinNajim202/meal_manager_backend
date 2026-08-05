<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PlatformFeedback;
use App\Helpers\Helper; // Assuming Helper exists for json responses like in CryptoStore
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Throwable;

class PlatformFeedbackController extends Controller
{
    /**
     * Store a newly created feedback in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'feedback'     => 'required|string',
                'email'        => 'nullable|email|max:255',
                'is_anonymous' => 'nullable|boolean',
            ]);

            // Default is_anonymous to false if not provided
            $is_anonymous = $request->input('is_anonymous', false);

            $feedback = PlatformFeedback::create([
                'feedback'     => $validated['feedback'],
                'email'        => $validated['email'] ?? null,
                'is_anonymous' => $is_anonymous,
            ]);

            return response()->json([
                'status'  => true,
                'code'    => 201,
                'message' => 'Thank you for your feedback!',
                'data'    => $feedback
            ], 201);

        } catch (ValidationException $e) {
            return Helper::jsonErrorResponse($e->errors(), 422, $e->getMessage());
        } catch (Throwable $e) {
            return Helper::jsonErrorResponse(
                config('app.debug') ? $e->getMessage() : 'Internal server error',
                500
            );
        }
    }
}
