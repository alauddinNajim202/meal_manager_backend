<?php

namespace App\Http\Controllers\Api\Auth;

use Exception;
use Carbon\Carbon;
use App\Models\User;
use App\Helpers\Helper;
use App\Traits\SMS;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{

    public $select;
    use SMS;
    public function __construct()
    {
        $this->select = ['id', 'name', 'email', 'otp', 'avatar'];
    }

    public function register(Request $request)
    {

        $request->validate([
            'name'          => 'required|string|max:100',
            'password'      => 'required|string|min:6',
            'phone'         => 'required|string|max:15|unique:users',

        ]);



         if ($request->has('email') && User::where('email', $request->input('email'))->exists()) {
            return Helper::jsonErrorResponse('Email already exists.', 422);
        }

        // Check phone
        if (User::where('phone', $request->input('phone'))->exists()) {
            return Helper::jsonErrorResponse('Phone number already exists.', 422);
        }

        // try {
        //     DB::beginTransaction();


            $user = User::create([
                'name'           => $request->input('name'),
                'slug'           => strtolower(Str::random(6)) . "-" . strtolower($request->input('name')),
                'email'          => "user" . rand(1000, 9999) . "@example.com",
                'phone'          => $request->input('phone'),
                'password'       => Hash::make($request->input('password')),
                'otp'            => rand(1000, 9999),
                'otp_expires_at' => Carbon::now()->addMinutes(60*5),
                'otp_verified_at'=> null,

            ]);


            // Send OTP via SMS to user's phone instead of email
            if (!empty($user->phone)) {
                $this->bdSms($user->phone, "Your OTP is: " . $user->otp);
            }

            return response()->json([
                'status'     => true,
                'message'    => 'User register in successfully.',
                'code'       => 200,
                'data' => $user
            ], 200);

        // } catch (Exception $e) {
        //     DB::rollBack();
        //     return Helper::jsonErrorResponse('User registration failed', 500, [$e->getMessage()]);
        // }
    }
    public function VerifyPhone(Request $request)
    {

        $request->validate([
            'phone' => 'required|string|exists:users,phone',
            'otp'   => 'required|digits:4',
        ]);
        // try {
            $user = User::where('phone', $request->input('phone'))->first();

            //! Check if phone has already been verified
            if (!empty($user->otp_verified_at)) {
                return  Helper::jsonErrorResponse('Phone already verified.', 409);
            }

            if ((string)$user->otp !== (string)$request->input('otp')) {
                return Helper::jsonErrorResponse('Invalid OTP code', 422);
            }

            //* Check if OTP has expired
            if (Carbon::parse($user->otp_expires_at)->isPast()) {
                return Helper::jsonErrorResponse('OTP has expired. Please request a new OTP.', 422);
            }

            //* Verify the phone
            $user->otp_verified_at   = now();
            $user->otp               = null;
            $user->otp_expires_at    = null;
            $user->save();

            $token = auth('api')->login($user);

            return Helper::jsonResponse(true, 'Phone verified successfully', 200, [
                'token_type' => 'bearer',
                'token' => $token,
                'expires_in' => auth('api')->factory()->getTTL() * 60,
                'data' => auth('api')->user()
            ]);
        // } catch (Exception $e) {
        //     return Helper::jsonErrorResponse($e->getMessage(), $e->getCode());
        // }
    }

    public function ResendOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|string|exists:users,phone',
        ]);

        try {
            $user = User::where('phone', $request->input('phone'))->first();

            if (!$user) {
                return Helper::jsonErrorResponse('User not found.', 404);
            }

            if ($user->otp_verified_at) {
                return Helper::jsonErrorResponse('Phone already verified.', 409);
            }

            $newOtp               = rand(1000, 9999);
            $otpExpiresAt         = Carbon::now()->addMinutes(60*5);
            $user->otp            = $newOtp;
            $user->otp_expires_at = $otpExpiresAt;
            $user->save();

            //* Send the new OTP to the user's phone
            $this->bdSms($user->phone, "Your OTP is: " . $newOtp);

            return Helper::jsonResponse(true, 'A new OTP has been sent to your phone.', 200);
        } catch (Exception $e) {
            return Helper::jsonErrorResponse($e->getMessage(), 200);
        }
    }
}
