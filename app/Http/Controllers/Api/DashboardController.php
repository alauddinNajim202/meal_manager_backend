<?php

namespace App\Http\Controllers\Api;

use App\Models\Meal;
use App\Models\Mess;
use App\Models\Expense;
use App\Models\Deposit;
use Carbon\Carbon;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $user = auth()->user();

        if (!$user->current_mess_id) {
            return $this->error(null, 'No active mess selected.', 400);
        }

        $messId = $user->current_mess_id;
        $month  = $request->month ?? Carbon::now()->month;
        $year   = $request->year  ?? Carbon::now()->year;

        $mess = Mess::find($messId);

        // Total Members (via pivot)
        $totalMembers = $mess->users()->count();

        // Today's Total Meals
        $todayMeals      = Meal::where('mess_id', $messId)->whereDate('date', Carbon::today())->get();
        $totalTodayMeals = $todayMeals->sum('breakfast') + $todayMeals->sum('lunch') + $todayMeals->sum('dinner');

        // This Month's Totals
        $totalExpenses = Expense::where('mess_id', $messId)
            ->whereMonth('date', $month)->whereYear('date', $year)->sum('amount');

        $totalDeposits = Deposit::where('mess_id', $messId)
            ->whereMonth('date', $month)->whereYear('date', $year)->sum('amount');

        $monthMeals      = Meal::where('mess_id', $messId)
            ->whereMonth('date', $month)->whereYear('date', $year)->get();
        $totalMonthMeals = $monthMeals->sum('breakfast') + $monthMeals->sum('lunch') + $monthMeals->sum('dinner');

        // Current Meal Rate
        $mealRate = $totalMonthMeals > 0 ? round($totalExpenses / $totalMonthMeals, 2) : 0;

        return $this->success([
            'mess'                => ['id' => $mess->id, 'name' => $mess->name],
            'total_members'       => $totalMembers,
            'today_meals'         => $totalTodayMeals,
            'monthly_expenses'    => $totalExpenses,
            'monthly_deposits'    => $totalDeposits,
            'monthly_total_meals' => $totalMonthMeals,
            'current_meal_rate'   => $mealRate,
        ], 'Dashboard data fetched', 200);
    }
}

