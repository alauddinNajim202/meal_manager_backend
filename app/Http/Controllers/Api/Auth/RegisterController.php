<?php

namespace App\Http\Controllers\Api\Auth;

use Exception;
use Carbon\Carbon;
use App\Models\User;
use App\Helpers\Helper;
use App\Traits\SMS;
use App\Traits\ApiResponse;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    use SMS, ApiResponse;

    protected array $select;

    public function __construct()
    {
        $this->select = ['id', 'name', 'email','phone', 'avatar', 'otp_verified_at', 'last_activity_at'];
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:100',
            'phone'    => 'required|string|max:15|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors()->first(), 'Validation failed', 422);
        }

        try {
            DB::beginTransaction();

            $user = User::create([
                'name'            => $request->name,
                'slug'            => Str::slug($request->name) . '-' . uniqid(),
                'email'           => 'user' . rand(1000, 9999) . '@example.com',
                'phone'           => $request->phone,
                'password'        => Hash::make($request->password),
                'otp'             => rand(1000, 9999),
                'otp_expires_at'  => now()->addMinutes(5),
                'otp_verified_at' => null,
            ]);

            // TODO: Send OTP via SMS to user's phone instead of email
            // if (!empty($user->phone)) {
            //     $this->bdSms($user->phone, "Your OTP is: {$user->otp}");
            // }

            DB::commit();

            return $this->success($user->only($this->select), 'User registered successfully. Please verify your phone number.', 200);

        } catch (Exception $e) {
            DB::rollBack();
            return Helper::jsonErrorResponse('User registration failed', 500, [$e->getMessage()]);
        }
    }

    public function VerifyPhone(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string|exists:users,phone',
            'otp'   => 'required|digits:4',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors()->first(), 'Validation failed', 422);
        }

        try {
            $user = User::where('phone', $request->phone)->first();

            if ($user->otp_verified_at) {
                return $this->error(null, 'Phone already verified.', 409);
            }

            if ((string) $user->otp !== (string) $request->otp) {
                return $this->error(null, 'Invalid OTP code', 422);
            }

            if (Carbon::parse($user->otp_expires_at)->isPast()) {
                return $this->error(null, 'OTP has expired. Please request a new OTP.', 422);
            }

            $user->update([
                'otp_verified_at' => now(),
                'otp'             => null,
                'otp_expires_at'  => null,
            ]);

            $token = auth('api')->login($user);
            




            return $this->success([
                'token_type' => 'bearer',
                'token'      => $token,
                'expires_in' => auth('api')->factory()->getTTL() * 60,
                'data'       => $user->only($this->select)
            ], 'Phone verified successfully', 200);

        } catch (Exception $e) {
            return Helper::jsonErrorResponse($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function ResendOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string|exists:users,phone',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors()->first(), 'Validation failed', 422);
        }

        try {
            $user = User::where('phone', $request->phone)->first();

            if ($user->otp_verified_at) {
                return $this->error(null, 'Phone already verified.', 409);
            }

            $newOtp = rand(1000, 9999);
            
            $user->update([
                'otp'            => $newOtp,
                'otp_expires_at' => now()->addMinutes(5),
            ]);

            // TODO: Send the new OTP to the user's phone
            // $this->bdSms($user->phone, "Your OTP is: {$newOtp}");

            return $this->success($user->only($this->select), 'A new OTP has been sent to your phone.', 200);

        } catch (Exception $e) {
            return $this->error(null, $e->getMessage(), $e->getCode() ?: 500);
        }
    }
}
