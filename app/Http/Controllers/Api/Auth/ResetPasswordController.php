<?php

namespace App\Http\Controllers\Api\Auth;

use Exception;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Str;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ResetPasswordController extends Controller
{
    use ApiResponse;

    protected array $select;

    public function __construct()
    {
        $this->select = ['id', 'name', 'email', 'otp', 'avatar','token', 'otp_verified_at', 'last_activity_at'];
    }

    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string|max:15|exists:users,phone',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors()->first(), 'Validation failed', 422);
        }
        
        try {
            $otp = rand(1000, 9999);
            $user = User::where('phone', $request->phone)->first();

            // TODO: Send OTP via SMS or Email
            // Mail::to($user->email)->send(new OtpMail($otp, $user, 'Reset Your Password'));

            $user->update([
                'otp'            => $otp,
                'otp_expires_at' => now()->addMinutes(60),
            ]);

            return $this->success($user->only($this->select), 'OTP sent to your phone.', 200);

        } catch (Exception $e) {
            return $this->error(null, $e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function MakeOtpToken(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string|max:15|exists:users,phone',
            'otp'   => 'required|digits:4',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors()->first(), 'Validation failed', 422);
        }

        try {
            $user = User::where('phone', $request->phone)->first();

            if (Carbon::parse($user->otp_expires_at)->isPast()) {
                return $this->error(null, 'OTP has expired.', 400);
            }

            if ((string) $user->otp !== (string) $request->otp) {
                return $this->error(null, 'Invalid OTP', 400);
            }

            $token = Str::random(160);

            $user->update([
                'otp'                            => null,
                'otp_expires_at'                 => null,
                'reset_password_token'           => $token,
                'reset_password_token_expire_at' => now()->addHour(),
            ]);

            return $this->success($user->only($this->select), 'OTP verified successfully.', 200);

        } catch (Exception $e) {
            return $this->error(null, $e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function ResetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone'    => 'required|string|max:15|exists:users,phone',
            'token'    => 'required|string',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors()->first(), 'Validation failed', 422);
        }
        
        try {
            $user = User::where('phone', $request->phone)->first();

            if (!empty($user->reset_password_token) && 
                $user->reset_password_token === $request->token && 
                Carbon::parse($user->reset_password_token_expire_at)->isFuture()) {

                $user->update([
                    'password'                       => Hash::make($request->password),
                    'reset_password_token'           => null,
                    'reset_password_token_expire_at' => null,
                    'otp'                            => null,
                    'otp_expires_at'                 => null,
                ]);

                return $this->success(null, 'Password reset successfully.', 200);
            }

            return $this->error(null, 'Invalid or expired Token', 400);

        } catch (Exception $e) {
            return $this->error(null, $e->getMessage(), $e->getCode() ?: 500);
        }
    }
}
