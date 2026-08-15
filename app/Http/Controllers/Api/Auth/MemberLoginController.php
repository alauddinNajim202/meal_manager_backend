<?php

namespace App\Http\Controllers\Api\Auth;

use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class MemberLoginController extends Controller
{
    use ApiResponse;

    /**
     * Step 1: Member enters phone → get their password.
     * Only works for users who are a member in at least one mess.
     */
    public function getPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors()->first(), 'Validation failed', 422);
        }

        $user = User::where('phone', $request->phone)->first();

        if (!$user) {
            return $this->error(null, 'No account found with this phone number.', 404);
        }

        // Check if user is a member in any mess
        $isMember = $user->messes()->exists();

        if (!$isMember) {
            return $this->error(null, 'This phone number is not registered as a mess member.', 404);
        }

        if (!$user->plain_password) {
            return $this->error(null, 'No password set. Please contact your mess manager.', 400);
        }

        return $this->success([
            'phone'    => $user->phone,
            'password' => $user->plain_password,
        ], 'Password retrieved successfully');
    }
}
