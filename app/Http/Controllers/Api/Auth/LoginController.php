<?php

namespace App\Http\Controllers\Api\Auth;

use App\Helpers\Helper;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use App\Traits\ApiResponse;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Throwable;

class LoginController extends Controller
{
    public $select;
    use ApiResponse;
    public function __construct()
    {
        parent::__construct();
        $this->select = ['id', 'name', 'email', 'avatar', 'otp_verified_at', 'last_activity_at'];
    }

    public function Login(Request $request)
    {
        try {
           $validator = Validator::make($request->all(), [
               'phone'    => 'required|exists:users,phone',
               'password' => 'required|string|min:6',
           ]);


            $user = User::where('phone', $request->phone)->first();

        
            if (!$user) {
                return $this->error(null, 'user is not active', 404);
            }

            //! Check the password
            if (!Hash::check($request->password, $user->password)) {
                return $this->error(null, 'Invalid password', 401);
            }

            //? Check if the email is verified before login is successful
            if (!$user->otp_verified_at) {
                return $this->error(null, 'Phone number not verified. Please verify your phone number before logging in.', 403);
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


            return $this->success([
                'token_type' => 'bearer',
                'token'      => $token,
                'expires_in' => auth('api')->factory()->getTTL() * 60,
                'data'       => auth('api')->user()
            ], 'Login successful', 200);

        } catch (ValidationException $e) {


            return $this->error(null, 'Validation failed', 422);
        } catch (Throwable $e) {

            return $this->error(null, 'Internal server error', 500);
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
