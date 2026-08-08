<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\Meal;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class MealController extends Controller
{
    use ApiResponse;

    /**
     * List meals for the current active mess filtered by month & year.
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        if (!$user->current_mess_id) {
            return $this->error(null, 'No active mess selected.', 400);
        }

        $validator = Validator::make($request->all(), [
            'month' => 'required|integer|min:1|max:12',
            'year'  => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors()->first(), 'Validation failed', 422);
        }

        $meals = Meal::where('mess_id', $user->current_mess_id)
            ->whereMonth('date', $request->month)
            ->whereYear('date', $request->year)
            ->with('user:id,name,avatar')
            ->get()
            ->map(function ($meal) {
                return [
                    'id'        => $meal->id,
                    'user'      => $meal->user,
                    'date'      => $meal->date,
                    'breakfast' => $meal->breakfast,
                    'lunch'     => $meal->lunch,
                    'dinner'    => $meal->dinner,
                    'total'     => $meal->total,
                    'is_guest'  => $meal->is_guest,
                ];
            });

        return $this->success($meals, 'Meals fetched successfully', 200);
    }

    /**
     * Store or update daily meals for a member.
     */
    public function store(Request $request)
    {
        $user = auth()->user();

        if (!$user->current_mess_id) {
            return $this->error(null, 'No active mess selected.', 400);
        }

        $validator = Validator::make($request->all(), [
            'user_id'   => 'required|exists:users,id',
            'date'      => 'required|date',
            'breakfast' => 'required|numeric|min:0',
            'lunch'     => 'required|numeric|min:0',
            'dinner'    => 'required|numeric|min:0',
            'is_guest'  => 'boolean',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors()->first(), 'Validation failed', 422);
        }

        try {
            $messId = $user->current_mess_id;

            // Verify the target user belongs to the same mess via pivot table
            $targetUser = User::findOrFail($request->user_id);
            $belongsToMess = $targetUser->messes()->where('mess_id', $messId)->exists();

            if (!$belongsToMess) {
                return $this->error(null, 'This user does not belong to your mess.', 403);
            }

            $meal = Meal::updateOrCreate(
                [
                    'user_id' => $request->user_id,
                    'mess_id' => $messId,
                    'date'    => $request->date,
                ],
                [
                    'breakfast' => $request->breakfast,
                    'lunch'     => $request->lunch,
                    'dinner'    => $request->dinner,
                    'is_guest'  => $request->is_guest ?? false,
                ]
            );

            return $this->success($meal, 'Meal saved successfully', 200);

        } catch (Exception $e) {
            return $this->error(null, $e->getMessage(), $e->getCode() ?: 500);
        }
    }

    /**
     * Remove the specified meal from storage.
     */
    public function destroy($id)
    {
        $user = auth()->user();

        try {
            $meal = Meal::where('id', $id)
                ->where('mess_id', $user->current_mess_id)
                ->firstOrFail();

            $meal->delete();

            return $this->success(null, 'Meal deleted successfully', 200);

        } catch (Exception $e) {
            return $this->error(null, $e->getMessage(), $e->getCode() ?: 500);
        }
    }
}

