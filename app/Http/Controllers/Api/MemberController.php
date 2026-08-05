<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class MemberController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $members = User::where('mess_id', $user->mess_id)->get();
        return response()->json(['status' => 'success', 'data' => $members]);
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        if ($user->role !== 'manager') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'phone' => 'nullable|string',
            'password' => 'required|string|min:6'
        ]);

        $newMember = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'slug' => str()->slug($request->name) . '-' . uniqid(),
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'mess_id' => $user->mess_id,
            'role' => 'member',
            'status' => 'active'
        ]);

        return response()->json(['status' => 'success', 'data' => $newMember]);
    }

    public function destroy($id)
    {
        $user = auth()->user();
        if ($user->role !== 'manager') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $member = User::where('id', $id)->where('mess_id', $user->mess_id)->firstOrFail();
        $member->delete();

        return response()->json(['status' => 'success', 'message' => 'Member removed']);
    }
}
