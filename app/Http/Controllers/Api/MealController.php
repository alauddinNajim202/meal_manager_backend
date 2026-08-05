<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Meal;

class MealController extends Controller
{
    /**
     * Display a listing of the meals for a specific month and year.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        
        $request->validate([
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer'
        ]);

        $meals = Meal::where('mess_id', $user->mess_id)
            ->whereMonth('date', $request->month)
            ->whereYear('date', $request->year)
            ->with('user:id,name,image')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $meals
        ]);
    }

    /**
     * Store or update daily meals for a member.
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'date' => 'required|date',
            'breakfast' => 'required|numeric|min:0',
            'lunch' => 'required|numeric|min:0',
            'dinner' => 'required|numeric|min:0',
            'is_guest' => 'boolean'
        ]);

        // Check if the user belongs to the same mess
        $targetUser = \App\Models\User::find($request->user_id);
        if ($targetUser->mess_id !== $user->mess_id) {
            return response()->json(['message' => 'User does not belong to your mess'], 403);
        }

        $meal = Meal::updateOrCreate(
            [
                'user_id' => $request->user_id,
                'mess_id' => $user->mess_id,
                'date' => $request->date,
            ],
            [
                'breakfast' => $request->breakfast,
                'lunch' => $request->lunch,
                'dinner' => $request->dinner,
                'is_guest' => $request->is_guest ?? false,
            ]
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Meal saved successfully',
            'data' => $meal
        ]);
    }

    /**
     * Remove the specified meal from storage.
     */
    public function destroy($id)
    {
        $user = auth()->user();
        $meal = Meal::where('id', $id)->where('mess_id', $user->mess_id)->firstOrFail();
        
        $meal->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Meal deleted successfully'
        ]);
    }
}
