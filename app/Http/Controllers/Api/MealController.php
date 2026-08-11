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
            'day'   => 'nullable|integer|min:1|max:31',
            'month' => 'nullable|integer|min:1|max:12',
            'year'  => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return $this->error(
                $validator->errors()->first(),
                'Validation failed',
                422
            );
        }

        $messId = $user->current_mess_id;

        // Get all members of current mess
        $members = User::whereHas('messes', function ($query) use ($messId) {
                $query->where('messes.id', $messId);
            })
            ->with([
                'meals' => function ($query) use ($messId, $request) {
                    $query->where('mess_id', $messId)
                        ->when($request->day, function ($query) use ($request) {
                            $query->whereDay('date', $request->day);
                        })
                        ->when($request->month, function ($query) use ($request) {
                            $query->whereMonth('date', $request->month);
                        })
                        ->when($request->year, function ($query) use ($request) {
                            $query->whereYear('date', $request->year);
                        });
                }
            ])
            ->select('id', 'name', 'avatar')
            ->get()
            ->map(function ($member) {

                return [
                    'user_member' => [
                        'id'     => $member->id,
                        'name'   => $member->name,
                        'avatar' => $member->avatar,
                    ],

                    'meals' => $member->meals->map(function ($meal) {
                        return [
                            'id'        => $meal->id,
                            'date'      => $meal->date,
                            'breakfast' => $meal->breakfast,
                            'lunch'     => $meal->lunch,
                            'dinner'    => $meal->dinner,
                            'total'     => $meal->total,
                            'is_guest'  => $meal->is_guest,
                        ];
                    })->values(),
                ];
            })
            ->values();

        return $this->success(
            $members,
            'Mess members and meals fetched successfully',
            200
        );
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
            'meals' => 'required|array|min:1',
            'meals.*.user_id' => 'required|exists:users,id',
            'meals.*.breakfast' => 'required|numeric|min:0',
            'meals.*.lunch' => 'required|numeric|min:0',
            'meals.*.dinner' => 'required|numeric|min:0',
            'meals.*.is_guest' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return $this->error(
                $validator->errors()->first(),
                'Validation failed',
                422
            );
        }

        try {
            $messId = $user->current_mess_id;

            $savedMeals = [];

            foreach ($request->meals as $mealData) {

                // Find target user
                $targetUser = User::find($mealData['user_id']);

                if (!$targetUser) {
                    return $this->error(
                        null,
                        'User not found.',
                        404
                    );
                }

                // Verify user belongs to same mess
                $belongsToMess = $targetUser
                    ->messes()
                    ->where('mess_id', $messId)
                    ->exists();

                if (!$belongsToMess) {
                    return $this->error(
                        null,
                        "User ID {$mealData['user_id']} does not belong to your mess.",
                        403
                    );
                }

                // Create or update meal
                $meal = Meal::updateOrCreate(
                    [
                        'user_id' => $mealData['user_id'],
                        'mess_id' => $messId,
                        'date' => $request->date,
                    ],
                    [
                        'breakfast' => $mealData['breakfast'],
                        'lunch' => $mealData['lunch'],
                        'dinner' => $mealData['dinner'],
                        'is_guest' => $mealData['is_guest'] ?? false,
                    ]
                );

                $savedMeals[] = $meal;
            }

            return $this->success(
                $savedMeals,
                'Meals saved successfully',
                200
            );

        } catch (Exception $e) {

            return $this->error(
                null,
                $e->getMessage(),
                500
            );
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

