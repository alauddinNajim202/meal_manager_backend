<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\User;
use App\Models\Mess;
use App\Helpers\Helper;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class MemberController extends Controller
{
    use ApiResponse;

    /**
     * List all members of the current active mess.
     */
    public function index()
    {
        $user = auth('api')->user();

        if (!$user->current_mess_id) {
            return $this->error(null, 'No active mess selected.', 400);
        }

        $mess = Mess::find($user->current_mess_id);
        $members = $mess->users()->withPivot('role', 'status')->get()
            ->map(function ($member) {
                return [
                    'id'     => $member->id,
                    'name'   => $member->name,
                    'phone'  => $member->phone,
                    'avatar' => $member->avatar,
                    'role'   => $member->pivot->role,
                    'status' => $member->pivot->status,
                ];
            });

        return $this->success($members, 'Members fetched successfully', 200);
    }

    /**
     * Add a member to the current active mess.
     * If the user exists (by phone), attach them.
     * If not, create a new account and then attach.
     */
    public function store(Request $request)
    {
        $authUser = auth('api')->user();

        if (!$this->isManagerOfCurrentMess($authUser)) {
            return $this->error(null, 'Only managers can add members.', 403);
        }

        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:100',
            'phone'    => 'required|string|max:15',
            'email'    => 'nullable|email',
            'nid'      => 'nullable|string|max:100',
            'nid_front'   => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'nid_back'    => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'avatar'   => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'emergency_contact_phone' => 'nullable|string|max:100',
            'advance_amount' => 'nullable|numeric',
            'month' => 'nullable|string|max:100',
            'joining_date' => 'nullable',
            'room_rent' => 'nullable|numeric',
            'notes' => 'nullable|string|max:255',
            

        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors()->first(), 'Validation failed', 422);
        }

        try {
            DB::beginTransaction();

            $messId = $authUser->current_mess_id;

            // Check if user already exists in the system
            $member = User::where('phone', $request->phone)->first();

            if ($member) {
                // Check if already a member of this mess
                $alreadyIn = $member->messes()->where('mess_id', $messId)->exists();
                if ($alreadyIn) {
                    return $this->error(null, 'This user is already a member of this mess.', 409);
                }
            } else {
                // Create a new user account
                $email = $request->email ?? $request->name . rand(1000, 9999) . '@gmail.com';

                if (User::where('email', $email)->exists()) {
                    $email = $request->name . rand(1000, 9999) . '@gmail.com';
                }

                $member = User::create([
                    'name'                    => $request->name,
                    'phone'                   => $request->phone,
                    'slug'                    => str()->slug($request->name) . '-' . uniqid(),
                    'email'                   => $email,
                    'avatar'                  => $request->avatar ? Helper::fileUpload($request->avatar, 'users', $request->name) : null,
                    'password'                => Hash::make(str()->random(10)),
                    
                ]);
            }

            $nidFrontPath = $request->nid_front ? Helper::fileUpload($request->nid_front, 'users/nid', $request->name . '-front') : null;
            $nidBackPath  = $request->nid_back ? Helper::fileUpload($request->nid_back, 'users/nid', $request->name . '-back') : null;

            // Attach user to this mess as a member via pivot table
            $member->messes()->attach($messId, [
                'role'           => 'member',
                'status'         => 'active',
                'nid'            => $request->nid,
                'nid_front'      => $nidFrontPath,
                'nid_back'       => $nidBackPath,
                'emergency_contact_phone' => $request->emergency_contact_phone,
                'advance_amount' => $request->advance_amount,
                'month'          => $request->month,
                'joining_date'   => $request->joining_date,
                'room_rent'      => $request->room_rent,
                'notes'          => $request->notes,
            ]);

            // If the member has no active mess, set this as their current mess
            if (!$member->current_mess_id) {
                $member->update(['current_mess_id' => $messId]);
            }

            $data = [
                'id'                      => $member->id,
                'name'                    => $member->name,
                'phone'                   => $member->phone,
                'avatar'                  => $member->avatar,
                'email'                   => $member->email,
                'nid'                     => $request->nid,
                'nid_front'               => $nidFrontPath,
                'nid_back'                => $nidBackPath,
                'emergency_contact_phone' => $request->emergency_contact_phone,
                'advance_amount'          => $request->advance_amount,
                'month'                   => $request->month,
                'joining_date'            => $request->joining_date,
                'room_rent'               => $request->room_rent,
                'notes'                   => $request->notes,
                'role'                    => 'member',
                'status'                  => 'active',
            ];
            DB::commit();

            return $this->success($data, 'Member added successfully', 201);

        } catch (Exception $e) {
            DB::rollBack();
            return $this->error(null, $e->getMessage(), 500);
        }
    }


    /**
     * Remove a member from the current active mess.
     * Does NOT delete their account, just detaches from the pivot table.
     */
    public function destroy($id)
    {
        $authUser = auth()->user();

        if (!$this->isManagerOfCurrentMess($authUser)) {
            return $this->error(null, 'Only managers can remove members.', 403);
        }

        try {
            $messId = $authUser->current_mess_id;
            $member = User::findOrFail($id);

            $belongsToMess = $member->messes()->where('mess_id', $messId)->exists();
            if (!$belongsToMess) {
                return $this->error(null, 'This user is not a member of your mess.', 404);
            }

            // Cannot remove yourself
            if ($member->id === $authUser->id) {
                return $this->error(null, 'You cannot remove yourself. Use Leave Mess instead.', 400);
            }

            // Detach from pivot table (account remains intact)
            $member->messes()->detach($messId);

            // If this was their active mess, clear it
            if ($member->current_mess_id == $messId) {
                $nextMess = $member->messes()->first();
                $member->update(['current_mess_id' => $nextMess?->id]);
            }

            return $this->success(null, 'Member removed from mess successfully.', 200);

        } catch (Exception $e) {
            return $this->error(null, $e->getMessage(), $e->getCode() ?: 500);
        }
    }

    /**
     * Change a member's role (manager <-> member).
     */
    public function changeRole(Request $request, $id)
    {
        $authUser = auth()->user();

        if (!$this->isManagerOfCurrentMess($authUser)) {
            return $this->error(null, 'Only managers can change roles.', 403);
        }

        $validator = Validator::make($request->all(), [
            'role' => 'required|in:manager,member',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors()->first(), 'Validation failed', 422);
        }

        try {
            $messId = $authUser->current_mess_id;
            $member = User::findOrFail($id);

            $belongsToMess = $member->messes()->where('mess_id', $messId)->exists();
            if (!$belongsToMess) {
                return $this->error(null, 'This user is not a member of your mess.', 404);
            }

            // Update the pivot table role
            $member->messes()->updateExistingPivot($messId, ['role' => $request->role]);

            return $this->success(null, "Role updated to '{$request->role}' successfully.", 200);

        } catch (Exception $e) {
            return $this->error(null, $e->getMessage(), $e->getCode() ?: 500);
        }
    }

    /**
     * Check if the authenticated user is a manager of their current mess.
     */
    private function isManagerOfCurrentMess(User $user): bool
    {
        if (!$user->current_mess_id) return false;

        $pivot = $user->messes()->where('mess_id', $user->current_mess_id)->first();
        return $pivot && $pivot->pivot->role === 'manager';
    }
}

