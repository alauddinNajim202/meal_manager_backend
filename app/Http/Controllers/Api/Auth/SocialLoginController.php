<?php

namespace App\Http\Controllers\Api\Auth;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class SocialLoginController extends Controller
{
    public $select;
    public function __construct()
    {
        parent::__construct();
        $this->select = ['id', 'name', 'email', 'avatar'];
    }

    public function RedirectToProvider($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    public function HandleProviderCallback($provider)
    {
        $data = Socialite::driver($provider)->stateless()->user();
        return $data;
    }

    public function SocialLogin(Request $request)
    {

        $request->validate([
            'token'         => 'required',
            'provider'      => 'required|in:google,facebook,apple',
        ]);


        $provider   = $request->provider;
        $socialUser = Socialite::driver($provider)->stateless()->userFromToken($request->token);


        if ($socialUser) {
            $user      = User::withTrashed()->where('email', $socialUser->email)->first();
            if (!empty($user->deleted_at)) {
                return Helper::jsonErrorResponse('Your account has been deleted.', 410);
            }
            $isNewUser = false;

            if (!$user) {
                $password = Str::random(16);
                /* if ($request->input('role') == 'trainer') {
                        $status = 'inactive';
                    } else {
                        $status = 'active';
                    } */
                $user     = User::create([
                    'name'              => $socialUser->getName() ? $socialUser->getName() : 'User'.rand(1000, 9999),
                    'email'             => $socialUser->getEmail(),
                    'password'          => bcrypt($password),
                    'avatar'            => $socialUser->getAvatar(),
                    'status'            => $status ?? 'active',
                    'slug' => Str::slug($socialUser->getName()) . '-' . Str::random(5),
                    'otp_verified_at' => now(),
                ]);

                $isNewUser = true;

            }

            Auth::login($user);
            $token = auth('api')->login($user);

            $data = User::select($this->select)->find($user->id);

            return response()->json([
                'status'     => true,
                'message'    => 'User logged in successfully.',
                'code'       => 200,
                'token_type' => 'bearer',
                'token'      => $token,
                'expires_in' => auth('api')->factory()->getTTL() * 60,
                'data'       => $data
            ], 200);
        } else {

            return Helper::jsonResponse(false, 'Unauthorized', 401);
        }
    }
}
