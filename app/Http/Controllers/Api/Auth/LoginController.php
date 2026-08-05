<?php

namespace App\Http\Controllers\Api\Auth;

use App\Helpers\Helper;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Exception;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Throwable;

class LoginController extends Controller
{
    public $select;
    public function __construct()
    {
        parent::__construct();
        $this->select = ['id', 'name', 'email', 'avatar', 'otp_verified_at', 'last_activity_at'];
    }

    public function Login(Request $request)
    {
        try {
           $request->validate([
               'phone'    => 'required|exists:users,phone',
               'password' => 'required|string|min:6',
           ]);


            $user = User::where('phone', $request->phone)->first();

             
            if ($user && $user->firebaseTokens) {
                $notifyData = ['title' => "Payment Failed", 'body'  => "test body", 'icon'  => config('settings.logo')];
                foreach ($user->firebaseTokens as $firebaseToken) {
                    Helper::sendNotifyMobile($firebaseToken->token, $notifyData);
                }
            }



            if (!$user) {
                return Helper::jsonResponse(false, 'user is not active', 404);
            }

            //! Check the password
            if (!Hash::check($request->password, $user->password)) {
                return Helper::jsonResponse(false, 'Invalid password', 401);
            }

            //? Check if the email is verified before login is successful
            if (!$user->otp_verified_at) {
                return Helper::jsonResponse(false, 'Email not verified. Please verify your email before logging in.', 403, ['is_otp_verified' => $user->isOtpVerified]);
            }else{
                $user->update([
                    'otp'            => null,
                    'otp_expires_at' => null,
                    'reset_password_token' => null,
                    'reset_password_token_expire_at' => null
                ]);
            }

            $user->update([
                'last_activity_at' => now(),
            ]);

            //* Generate token if email is verified
            $token = auth('api')->login($user);

            $data = User::select($this->select)->find(auth('api')->user()->id);


            return response()->json([
                'status'     => true,
                'message'    => 'Login successful',
                'code'       => 200,
                'expires_in' => auth('api')->factory()->getTTL(),
                'token'      => $token,
                'data'       => $data,
            ], 200);

        } catch (ValidationException $e) {
            DB::rollBack();

            return Helper::jsonErrorResponse($e->errors(), 422,$e->getMessage());
        } catch (Throwable $e) {
            DB::rollBack();

            return Helper::jsonErrorResponse(
                config('app.debug') ? $e->getMessage() : 'Internal server error',
                500
            );
        }
    }

    public function refreshToken()
    {
        $refreshToken = auth('api')->refresh();

        if (empty($refreshToken)) {
            return Helper::jsonErrorResponse('Failed to refresh the token.', 401);
        }

        return response()->json([
            'status'     => true,
            'message'    => 'Access token refreshed successfully.',
            'code'       => 200,
            'token_type' => 'bearer',
            'token'      => $refreshToken,
            'expires_in' => auth('api')->factory()->getTTL() * 60,
            'data' => auth('api')->user()
        ]);
    }

}
