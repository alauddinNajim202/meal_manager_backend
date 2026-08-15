<?php

namespace App\Http\Middleware;

use Closure;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class MessManagerMiddleware
{
    use ApiResponse;

    /**
     * Only allow managers of the current mess to proceed.
     * Members get a 403 Forbidden response.
     */
    public function handle(Request $request, Closure $next)
    {
        $user = auth('api')->user();

        if (!$user) {
            return $this->error(null, 'Unauthenticated.', 401);
        }

        if (!$user->current_mess_id) {
            return $this->error(null, 'No active mess selected.', 400);
        }

        $pivot = $user->messes()->where('mess_id', $user->current_mess_id)->first();

        if (!$pivot || $pivot->pivot->role !== 'manager') {
            return $this->error(null, 'Only managers can perform this action.', 403);
        }

        return $next($request);
    }
}
