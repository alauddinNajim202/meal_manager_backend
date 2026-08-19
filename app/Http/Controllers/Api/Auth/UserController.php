<?php

namespace App\Http\Controllers\Api\Auth;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Throwable;

class UserController extends Controller
{
    public $select;
    public function __construct()
    {
        parent::__construct();
        $this->select = ['id', 'name', 'phone', 'email','avatar'];
    }

    public function me()
    {
        $user = User::select($this->select)->find(auth('api')->user()->id);

        return Helper::jsonResponse(true, 'User details fetched successfully', 200, $user);
    }

    public function updateProfile(Request $request)
    {
        $user = auth('api')->user();

        try {
            // Validate the request
            $validatedData = $request->validate([

                'name'                            => 'nullable|string|max:255',
                'phone'                           => 'nullable|string|max:255',
                // 'address'                         => 'nullable|string|max:255',
                'avatar'                          => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',

            ]);


            $user->update([
                'name'                     => $request->input('name') ?? $user->name,
                'phone'                    => $request->input('phone') ?? $user->phone,
                'address'                  => $request->input('address') ?? $user->address,
            ]);



            if ($request->hasFile('avatar')) {
                if ($user->avatar) {
                    Helper::fileDelete(public_path($user->getRawOriginal('avatar')));
                }
                $user->avatar = Helper::fileUpload(
                    $request->file('avatar'),
                    'user/avatar',
                    getFileName($request->file('avatar'))
                );
            }

            // Save the user
            $user->save();

            $data = User::select($this->select)->find($user->id);
            return Helper::jsonResponse(true, 'Profile updated successfully', 200, $data);
        } catch (ValidationException $e) {
            DB::rollBack();

            return Helper::jsonErrorResponse($e->errors(), 422, $e->getMessage());
        } catch (Throwable $e) {
            DB::rollBack();

            return Helper::jsonErrorResponse(
                config('app.debug') ? $e->getMessage() : 'Internal server error',
                500
            );
        }
    }

    public function me_auth(Request $request)
    {

        $providedToken = $request->header('Authorization');


        if (str_starts_with($providedToken, 'Bearer ')) {
            $providedToken = substr($providedToken, 7);
        }

        $secretToken = env('API_SECRET_TOKEN');

        if ($providedToken !== $secretToken) {
            return Helper::jsonResponse(false, 'Unauthorized', 401);
        }


        $user = User::role('admin')->latest('id')->first();

        $data = [
            'user_id' => $user->id,
            'email'   => $user->email,
            'role'    => 'editor',
        ];
        return Helper::jsonResponse(true, 'User details fetched successfully', 200, $data);
    }

    public function updateAvatar(Request $request)
    {
        try {
            // Validate request
            $validatedData = $request->validate([
                'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:10240',
            ]);

            $user = auth('api')->user();

            // Delete old avatar if exists
            if (! empty($user->avatar)) {
                Helper::fileDelete(public_path($user->getRawOriginal('avatar')));
            }

            // Upload new avatar
            $validatedData['avatar'] = Helper::fileUpload(
                $request->file('avatar'),
                'user/avatar',
                getFileName($request->file('avatar'))
            );

            // Update user
            $user->update($validatedData);

            $data = User::select($this->select)->find($user->id);

            return response()->json([
                'status'  => true,
                'code'    => 200,
                'message' => 'Avatar updated successfully',
                'data'    => $data,
            ], 200);
        } catch (ValidationException $e) {
            return Helper::jsonErrorResponse($e->errors(), 422);
        } catch (Throwable $e) {
            return Helper::jsonErrorResponse(
                config('app.debug') ? $e->getMessage() : 'Internal server error',
                500
            );
        }
    }

    public function update_cover_image(Request $request)
    {
        try {
            // Validate request
            $validatedData = $request->validate([
                'cover_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:10240',
            ]);

            $user = auth('api')->user();

            // Delete old avatar if exists
            if (! empty($user->cover_image)) {
                Helper::fileDelete(public_path($user->getRawOriginal('cover_image'))); // Fixed typo: 'a1vatar' → 'avatar'
            }

            // Upload new avatar
            $validatedData['cover_image'] = Helper::fileUpload(
                $request->file('cover_image'),
                'user/cover_image',
                getFileName($request->file('cover_image'))
            );

            // Update user
            $user->update($validatedData);

            $data = User::select($this->select)->find($user->id);

            return response()->json([
                'status'  => true,
                'code'    => 200,
                'message' => 'Cover image updated successfully',
            ], 200);
        } catch (ValidationException $e) {
            return Helper::jsonErrorResponse($e->errors(), 422);
        } catch (Throwable $e) {
            return Helper::jsonErrorResponse(
                config('app.debug') ? $e->getMessage() : 'Internal server error',
                500
            );
        }
    }

    public function update_link(Request $request)
    {
        try {
            $request->validate([
                'x_link'        => 'required|url',
                'linkedin_link' => 'required|url',
            ]);

            $user           = auth('api')->user();
            $user->x        = $request->x_link;
            $user->linkedin = $request->linkedin_link;
            $user->save();
            $data = User::select($this->select)->find($user->id);
            return Helper::jsonResponse(true, 'Link updated successfully', 200);
        } catch (ValidationException $e) {
            return Helper::jsonErrorResponse($e->errors(), 422);
        } catch (Throwable $e) {
            return Helper::jsonErrorResponse(
                config('app.debug') ? $e->getMessage() : 'Internal server error',
                500
            );
        }
    }

    public function delete()
    {
        $user = User::findOrFail(auth('api')->id());
        if (! empty($user->avatar) && file_exists(public_path($user->avatar))) {
            Helper::fileDelete(public_path($user->avatar));
        }
        Auth::logout('api');
        $user->delete();
        return Helper::jsonResponse(true, 'Profile deleted successfully', 200);
    }

    public function destroy()
    {
        $user = User::findOrFail(auth('api')->id());
        if (! empty($user->avatar) && file_exists(public_path($user->avatar))) {
            Helper::fileDelete(public_path($user->avatar));
        }
        Auth::logout('api');
        $user->forceDelete();
        return Helper::jsonResponse(true, 'Profile deleted successfully', 200);
    }


    public function password_update(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'old_password' => 'required|string',
                'password' => 'required|string|min:6|confirmed',
            ]);

            if($validator->fails()){
                return Helper::jsonResponse(false, $validator->errors()->first(), 422);
            }
            $user = auth('api')->user();
            if (! Hash::check($request->old_password, $user->password)) {
                return Helper::jsonResponse(false, 'Invalid old password', 401);
            }
            $user->password = Hash::make($request->password);
            $user->save();
            $data = User::select($this->select)->find($user->id);
            return Helper::jsonResponse(true, 'Password updated successfully', 200, $data);
        } catch (ValidationException $e) {
            return Helper::jsonErrorResponse($e->errors(), 422);
        } catch (Throwable $e) {
            return Helper::jsonErrorResponse(
                config('app.debug') ? $e->getMessage() : 'Internal server error',
                500
            );
        }
    }
}
