<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MessOwnerMiddleware
{
    use \App\Traits\ApiResponse;

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth('api')->user();

        if (!$user) {
            return $this->error(null, 'Unauthenticated.', 401);
        }

        if (!$user->current_mess_id) {
            return $this->error(null, 'No active mess selected.', 400);
        }

        $pivot = $user->messes()->where('mess_id', $user->current_mess_id)->first();

        if (!$pivot || $pivot->pivot->role !== 'owner') {
            return $this->error(null, 'Only owners can perform this action.', 403);
        }

        return $next($request);
    }
}
