<?php

namespace App\Http\Controllers\Api\Frontend;

use Exception;
use App\Models\Mess;
use App\Helpers\Helper;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class MessController extends Controller
{
    use ApiResponse;
    /**
     * List all messes the authenticated user belongs to.
     */
    public function index()
    {
        $user   = auth('api')->user();
        $messes = $user->messes()->get();

        $data = $messes->map(function ($mess) use ($user) {
            return [
                'id' => $mess->id,
                'name' => $mess->name,
                'address' => $mess->address,
                'image' => $mess->image,
                'role' => $mess->pivot->role,
                'is_current' => $mess->id == $user->current_mess_id,
                // 'pivot' => $mess->pivot,
            ];
        });




        

        return $this->success($data, 'Mess list retrieved successfully', 200);
    }

    /**
     * Create a new mess and assign the creator as manager.
     * Also sets it as the user's current active mess.
     */
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'    => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors()->first(), 'Validation failed', 422);
        }

        try {
            DB::beginTransaction();

            $user = auth()->user();

            // Create the new mess
            $mess = Mess::create([
                'name'    => $request->name,
                'address' => $request->address,
                'image' => $request->hasFile('image') ? Helper::fileUpload($request->image, 'messes', $request->name) : null,
            ]);

            // Attach the creator as manager in the pivot table
            $user->messes()->attach($mess->id, [
                'role'   => 'manager',
                'status' => 'active',
            ]);

            // Set this new mess as the user's current active mess
            $user->update(['current_mess_id' => $mess->id]);

            DB::commit();

            return $this->success($mess, 'Mess created successfully', 201);
        } catch (Exception $e) {
            DB::rollBack();
            return $this->error(null, $e->getMessage(), $e->getCode() ?: 500);
        }
    }

    /**
     * Switch the user's currently active mess.
     */
    public function switchMess(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mess_id' => 'required|exists:messes,id',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors()->first(), 'Validation failed', 422);
        }

        try {
            $user = auth()->user();

            // Verify the user actually belongs to this mess
            $belongs = $user->messes()->where('mess_id', $request->mess_id)->exists();

            if (!$belongs) {
                return $this->error(null, 'You are not a member of this mess.', 403);
            }

            $user->update(['current_mess_id' => $request->mess_id]);
            $mess = Mess::find($request->mess_id);

            return $this->success($mess, 'Switched to mess: ' . $mess->name, 200);
        } catch (Exception $e) {
            return $this->error(null, $e->getMessage(), $e->getCode() ?: 500);
        }
    }

    /**
     * Leave a mess (cannot leave if you are the only manager).
     */
    public function leave(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mess_id' => 'required|exists:messes,id',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors()->first(), 'Validation failed', 422);
        }

        try {
            $user = auth()->user();
            $messId = $request->mess_id;

            $pivot = $user->messes()->where('mess_id', $messId)->first();
            if (!$pivot) {
                return $this->error(null, 'You are not a member of this mess.', 403);
            }

            // Prevent leaving if user is the only manager
            if ($pivot->pivot->role === 'manager') {
                $managerCount = Mess::find($messId)->managers()->count();
                if ($managerCount <= 1) {
                    return $this->error(null, 'You are the only manager. Please assign another manager before leaving.', 400);
                }
            }

            $user->messes()->detach($messId);

            // If leaving the current mess, clear current_mess_id
            if ($user->current_mess_id == $messId) {
                $nextMess = $user->messes()->first();
                $user->update(['current_mess_id' => $nextMess?->id]);
            }

            return $this->success(null, 'You have left the mess successfully.', 200);
        } catch (Exception $e) {
            return $this->error(null, $e->getMessage(), $e->getCode() ?: 500);
        }
    }
}
