<?php

namespace App\Http\Controllers\Api\Auth;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Throwable;

class UserController extends Controller
{
    public $select;
    public function __construct()
    {
        parent::__construct();
        $this->select = ['id', 'name', 'phone', 'email', 'avatar', 'dob', 'address', 'nid_number', 'religion', 'gender', 'profession', 'division_id', 'district_id', 'upazila_id', 'whatsapp_number'];
    }

    public function me()
    {
        $user = User::select($this->select)->find(auth('api')->user()->id);

        $data = [
            'total_properties' => $user->properties()->count(),
            'total_favorites' => $user->favourites()->count(),
            'total_views' => 0,
            'user' => $user,
        ];




        return Helper::jsonResponse(true, 'User details fetched successfully', 200, $data);
    }

    public function updateProfile(Request $request)
    {
        $user = auth('api')->user();

        try {
            // Validate the request
            $validatedData = $request->validate([

                'avatar'                          => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
                'name'                            => 'nullable|string|max:255',
                'phone'                           => 'nullable|string|max:255',
                'address'                         => 'nullable|string|max:255',
                'division_id'                     => 'nullable|exists:divisions,id',
                'district_id'                     => 'nullable|exists:districts,id',
                'upazila_id'                      => 'nullable|exists:upazilas,id',
                'religion'                        => 'nullable|string|max:255',
                'gender'                          => 'nullable|string|max:255',
                'whatsapp_number'                 => 'nullable|string|max:255',
                'nid_number'                      => 'nullable|string|max:255',
                'referred_by_id'                  => 'nullable|exists:users,id',

            ]);


            $user->update([
                'name'                     => $request->input('name') ?? $user->name,
                'phone'                    => $request->input('phone') ?? $user->phone,
                'address'                  => $request->input('address') ?? $user->address,
                'division_id'              => $request->input('division_id') ?? $user->division_id,
                'district_id'              => $request->input('district_id') ?? $user->district_id,
                'upazila_id'               => $request->input('upazila_id') ?? $user->upazila_id,
                'religion'                 => $request->input('religion') ?? $user->religion,
                'gender'                   => $request->input('gender') ?? $user->gender,
                'whatsapp_number'          => $request->input('whatsapp_number') ?? $user->whatsapp_number,
                'nid_number'               => $request->input('nid_number') ?? $user->nid_number,
                'referred_by_id'           => $request->input('referred_by_id') ?? $user->referred_by_id,
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
            $request->validate([
                'old_password' => 'required|string',
                'password' => 'required|string|min:6|confirmed',
            ]);

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
