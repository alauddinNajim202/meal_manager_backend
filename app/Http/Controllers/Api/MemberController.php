<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\User;
use App\Models\Mess;
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
        $user = auth()->user();

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
        $authUser = auth()->user();

        if (!$this->isManagerOfCurrentMess($authUser)) {
            return $this->error(null, 'Only managers can add members.', 403);
        }

        $validator = Validator::make($request->all(), [
            'phone'    => 'required|string|max:15',
            'name'     => 'required_if:phone,null|string|max:100',
            'password' => 'nullable|string|min:6',
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
                $member = User::create([
                    'name'            => $request->name,
                    'phone'           => $request->phone,
                    'slug'            => str()->slug($request->name) . '-' . uniqid(),
                    'email'           => 'user' . rand(1000, 9999) . '@example.com',
                    'password'        => Hash::make($request->password ?? str()->random(10)),
                    'otp'             => rand(1000, 9999),
                    'otp_expires_at'  => now()->addMinutes(5),
                ]);
            }

            // Attach user to this mess as a member via pivot table
            $member->messes()->attach($messId, [
                'role'   => 'member',
                'status' => 'active',
            ]);

            // If the member has no active mess, set this as their current mess
            if (!$member->current_mess_id) {
                $member->update(['current_mess_id' => $messId]);
            }

            DB::commit();

            return $this->success([
                'id'    => $member->id,
                'name'  => $member->name,
                'phone' => $member->phone,
                'role'  => 'member',
            ], 'Member added successfully', 201);

        } catch (Exception $e) {
            DB::rollBack();
            return $this->error(null, $e->getMessage(), $e->getCode() ?: 500);
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

