<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Meal;
use App\Models\Expense;
use App\Models\Deposit;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        if (!$user || !$user->mess_id) {
            return response()->json(['message' => 'No mess associated'], 400);
        }

        $messId = $user->mess_id;
        $month = $request->month ?? Carbon::now()->month;
        $year = $request->year ?? Carbon::now()->year;

        // Total Members
        $totalMembers = User::where('mess_id', $messId)->count();

        // Today's Total Meals
        $todayMeals = Meal::where('mess_id', $messId)
            ->whereDate('date', Carbon::today())
            ->get();
        $totalTodayMeals = $todayMeals->sum('breakfast') + $todayMeals->sum('lunch') + $todayMeals->sum('dinner');

        // This Month's Total Expenses
        $totalExpenses = Expense::where('mess_id', $messId)
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->sum('amount');

        // This Month's Total Deposits
        $totalDeposits = Deposit::where('mess_id', $messId)
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->sum('amount');

        // Total Meals this Month
        $monthMeals = Meal::where('mess_id', $messId)
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->get();
        $totalMonthMeals = $monthMeals->sum('breakfast') + $monthMeals->sum('lunch') + $monthMeals->sum('dinner');

        // Current Meal Rate
        $mealRate = $totalMonthMeals > 0 ? round($totalExpenses / $totalMonthMeals, 2) : 0;

        return response()->json([
            'status' => 'success',
            'data' => [
                'total_members' => $totalMembers,
                'today_meals' => $totalTodayMeals,
                'monthly_expenses' => $totalExpenses,
                'monthly_deposits' => $totalDeposits,
                'monthly_total_meals' => $totalMonthMeals,
                'current_meal_rate' => $mealRate,
            ]
        ]);
    }
}
