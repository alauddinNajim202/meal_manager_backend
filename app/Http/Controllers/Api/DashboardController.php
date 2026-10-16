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
        $month  = (int) ($request->month ?: Carbon::now()->month);
        $year   = (int) ($request->year ?: Carbon::now()->year);

        $mess = Mess::find($messId);

        // Check if the requested month is closed
        $monthlyReport = \App\Models\MonthlyReport::where('mess_id', $messId)
            ->where('month', $month)
            ->where('year', $year)
            ->first();
        $isMonthClosed = $monthlyReport && $monthlyReport->status === 'finalized';

        // Total Members (via pivot)
        $totalMembers = $mess->users()->count();

        // Today's Total Meals
        $todayMeals      = Meal::where('mess_id', $messId)->whereDate('date', Carbon::today())->get();
        $totalTodayMeals = $todayMeals->sum('breakfast') + $todayMeals->sum('lunch') + $todayMeals->sum('dinner');

        // This Month's Totals
        $totalExpenses = (float) Expense::where('mess_id', $messId)
            ->whereMonth('date', $month)->whereYear('date', $year)->sum('amount');

        $totalDeposits = (float) Deposit::where('mess_id', $messId)
            ->whereMonth('date', $month)->whereYear('date', $year)->sum('amount');

        $monthMeals      = Meal::where('mess_id', $messId)
            ->whereMonth('date', $month)->whereYear('date', $year)->get();
        $totalMonthMeals = $monthMeals->sum('breakfast') + $monthMeals->sum('lunch') + $monthMeals->sum('dinner');

        // Current Meal Rate
        $mealRate = $totalMonthMeals > 0 ? round($totalExpenses / $totalMonthMeals, 2) : 0;

        // Login user's assigned bazar dates for this month
        $myBazarSchedules = \App\Models\BazarSchedule::where('mess_id', $messId)
            ->where('user_id', $user->id)
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->orderBy('date')
            ->get()
            ->map(function ($s) {
                return [
                    'id'             => $s->id,
                    'date'           => Carbon::parse($s->date)->format('Y-m-d'),
                    'formatted_date' => Carbon::parse($s->date)->format('j M, Y'),
                    'status'         => $s->status
                ];
            });

        // User's own meal and deposit summary for this month
        $myMonthMeals = Meal::where('mess_id', $messId)
            ->where('user_id', $user->id)
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->get();
        $myTotalMeals   = $myMonthMeals->sum('breakfast') + $myMonthMeals->sum('lunch') + $myMonthMeals->sum('dinner');
        $myTotalDeposit = (float) Deposit::where('mess_id', $messId)
            ->where('user_id', $user->id)
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->sum('amount');
        $myTotalCost = round($myTotalMeals * $mealRate, 2);
        $myBalance   = round($myTotalDeposit - $myTotalCost, 2);

        // Last month's history summary
        $prevDate = Carbon::createFromDate($year, $month, 1)->subMonth();
        $lastMonthReport = \App\Models\MonthlyReport::where('mess_id', $messId)
            ->where('month', $prevDate->month)
            ->where('year', $prevDate->year)
            ->first();
        $lastMonthSummary = $lastMonthReport ? [
            'month'       => $prevDate->month,
            'month_name'  => $prevDate->format('F'),
            'year'        => $prevDate->year,
            'total_meals' => (float) $lastMonthReport->total_meals,
            'total_cost'  => (float) $lastMonthReport->total_cost,
            'meal_rate'   => (float) $lastMonthReport->meal_rate,
            'status'      => $lastMonthReport->status,
        ] : null;

        return $this->success([
            'mess'                => ['id' => $mess->id, 'name' => $mess->name],
            'month'               => $month,
            'month_name'          => Carbon::createFromDate($year, $month, 1)->format('F'),
            'year'                => $year,
            'is_month_closed'     => $isMonthClosed,
            'total_members'       => $totalMembers,
            'today_meals'         => $totalTodayMeals,
            'monthly_expenses'    => $totalExpenses,
            'monthly_deposits'    => $totalDeposits,
            'monthly_total_meals' => $totalMonthMeals,
            'current_meal_rate'   => $mealRate,
            'my_bazar_dates'      => $myBazarSchedules,
            'my_summary'          => [
                'total_meals'    => $myTotalMeals,
                'total_deposit'  => $myTotalDeposit,
                'estimated_cost' => $myTotalCost,
                'balance'        => $myBalance,
            ],
            'last_month_summary'  => $lastMonthSummary,
        ], 'Dashboard data fetched', 200);
    }
}

