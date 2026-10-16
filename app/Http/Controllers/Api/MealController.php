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

        $month = $request->month ?? \Carbon\Carbon::now()->month;
        $year = $request->year ?? \Carbon\Carbon::now()->year;

        // Get all members of current mess
        $members = User::whereHas('messes', function ($query) use ($messId) {
        $query->where('messes.id', $messId);
                })
                ->with([
                    'meals' => function ($query) use ($messId, $request, $month, $year) {
                        $query->where('mess_id', $messId)
                            ->whereMonth('date', $month)
                            ->whereYear('date', $year)
                            ->when($request->day, function ($query) use ($request) {
                                $query->whereDay('date', $request->day);
                            });
                    }
                ])
                ->select('id', 'name', 'avatar')
                ->get()
                ->map(function ($member) {

                    $meal = $member->meals->first();

                    return [
                        'user_member' => [
                            'id'     => $member->id,
                            'name'   => $member->name,
                            'avatar' => $member->avatar,
                        ],

                        'meal' => [
                            'id'        => $meal?->id,
                            'date'      => $meal?->date,
                            'breakfast' => $meal?->breakfast ?? "0",
                            'lunch'     => $meal?->lunch ?? "0",
                            'dinner'    => $meal?->dinner ?? "0",
                            'total'     => $meal?->total ?? "0",
                            'is_guest'  => $meal?->is_guest ?? false,
                        ],
                    ];
                });

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

            $dateObj = \Carbon\Carbon::parse($request->date);
            $isFinalized = \App\Models\MonthlyReport::where('mess_id', $messId)
                ->where('month', $dateObj->month)
                ->where('year', $dateObj->year)
                ->where('status', 'finalized')
                ->exists();

            if ($isFinalized) {
                return $this->error(
                    null,
                    "Cannot save meals for date {$request->date}. The month ({$dateObj->format('F Y')}) is already closed/finalized.",
                    422
                );
            }

            $savedMeals = [];
            $mealDetails = [];

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

                // Collect details for notification
                $breakfastText = $mealData['breakfast'] > 0 ? "+{$mealData['breakfast']}" : $mealData['breakfast'];
                $lunchText = $mealData['lunch'] > 0 ? "+{$mealData['lunch']}" : $mealData['lunch'];
                $dinnerText = $mealData['dinner'] > 0 ? "+{$mealData['dinner']}" : $mealData['dinner'];
                $mealDetails[] = "{$targetUser->name} (B: {$breakfastText}, L: {$lunchText}, D: {$dinnerText})";
            }
            
            $mess = \App\Models\Mess::find($messId);
            if ($mess && !empty($mealDetails)) {
                $detailsString = implode('<br>', $mealDetails);
                $mess->notify(new \App\Notifications\MealCountUpdatedNotification($user->name, $request->date, $detailsString));
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
     * Update meals for a single member across multiple dates.
     * Allowed only if the corresponding month is not closed.
     */
    public function updateMemberMeals(Request $request)
    {
        $user = auth()->user();

        if (!$user->current_mess_id) {
            return $this->error(null, 'No active mess selected.', 400);
        }

        $validator = Validator::make($request->all(), [
            'user_id'          => 'required_without:member_id|nullable|exists:users,id',
            'member_id'        => 'required_without:user_id|nullable|exists:users,id',
            'meals'            => 'required|array|min:1',
            'meals.*.date'      => 'required|date',
            'meals.*.breakfast' => 'required|numeric|min:0',
            'meals.*.lunch'     => 'required|numeric|min:0',
            'meals.*.dinner'    => 'required|numeric|min:0',
            'meals.*.is_guest'  => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors()->first(), 'Validation failed', 422);
        }

        try {
            $messId = $user->current_mess_id;
            $targetUserId = $request->user_id ?? $request->member_id;
            $targetUser = User::find($targetUserId);

            if (!$targetUser) {
                return $this->error(null, 'Member not found.', 404);
            }

            // Verify member belongs to this mess
            $belongsToMess = $targetUser->messes()->where('mess_id', $messId)->exists();
            if (!$belongsToMess) {
                return $this->error(null, 'This user does not belong to your mess.', 403);
            }

            // Verify each date's month is not finalized
            foreach ($request->meals as $m) {
                $dateObj = \Carbon\Carbon::parse($m['date']);
                $isFinalized = \App\Models\MonthlyReport::where('mess_id', $messId)
                    ->where('month', $dateObj->month)
                    ->where('year', $dateObj->year)
                    ->where('status', 'finalized')
                    ->exists();

                if ($isFinalized) {
                    return $this->error(
                        null,
                        "Cannot update meal for date {$m['date']}. The month ({$dateObj->format('F Y')}) is already closed/finalized.",
                        422
                    );
                }
            }

            $savedMeals = [];
            foreach ($request->meals as $m) {
                $saved = Meal::updateOrCreate(
                    [
                        'user_id' => $targetUserId,
                        'mess_id' => $messId,
                        'date'    => $m['date'],
                    ],
                    [
                        'breakfast' => $m['breakfast'],
                        'lunch'     => $m['lunch'],
                        'dinner'    => $m['dinner'],
                        'is_guest'  => $m['is_guest'] ?? false,
                    ]
                );
                $savedMeals[] = $saved;
            }

            return $this->success($savedMeals, "Meals for {$targetUser->name} updated successfully across " . count($savedMeals) . ' dates.', 200);

        } catch (Exception $e) {
            return $this->error(null, $e->getMessage(), 500);
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

