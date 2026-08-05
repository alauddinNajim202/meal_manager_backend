<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\MonthlyReport;
use App\Models\UserMonthlyBill;
use App\Models\Meal;
use App\Models\Expense;
use App\Models\Deposit;
use App\Models\User;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $month = $request->month ?? now()->month;
        $year = $request->year ?? now()->year;

        $report = MonthlyReport::where('mess_id', $user->mess_id)
            ->where('month', $month)
            ->where('year', $year)
            ->with('userMonthlyBills.user:id,name')
            ->first();

        return response()->json(['status' => 'success', 'data' => $report]);
    }

    public function myBill(Request $request)
    {
        $user = auth()->user();
        $month = $request->month ?? now()->month;
        $year = $request->year ?? now()->year;

        $report = MonthlyReport::where('mess_id', $user->mess_id)
            ->where('month', $month)
            ->where('year', $year)
            ->first();

        if (!$report) {
            return response()->json(['message' => 'Report not generated yet'], 404);
        }

        $myBill = UserMonthlyBill::where('monthly_report_id', $report->id)
            ->where('user_id', $user->id)
            ->first();

        return response()->json(['status' => 'success', 'data' => $myBill]);
    }

    public function generate(Request $request)
    {
        $user = auth()->user();
        if ($user->role !== 'manager') {
            return response()->json(['message' => 'Only managers can generate reports'], 403);
        }

        $request->validate([
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer'
        ]);

        $messId = $user->mess_id;
        $month = $request->month;
        $year = $request->year;

        $allMeals = Meal::where('mess_id', $messId)->whereMonth('date', $month)->whereYear('date', $year)->get();
        $totalMeals = $allMeals->sum('breakfast') + $allMeals->sum('lunch') + $allMeals->sum('dinner');

        $totalCost = Expense::where('mess_id', $messId)->whereMonth('date', $month)->whereYear('date', $year)->sum('amount');
        
        $mealRate = $totalMeals > 0 ? ($totalCost / $totalMeals) : 0;

        $report = MonthlyReport::updateOrCreate(
            ['mess_id' => $messId, 'month' => $month, 'year' => $year],
            ['total_meals' => $totalMeals, 'total_cost' => $totalCost, 'meal_rate' => $mealRate, 'status' => 'finalized']
        );

        $members = User::where('mess_id', $messId)->get();
        
        foreach ($members as $member) {
            $memberMeals = $allMeals->where('user_id', $member->id);
            $userTotalMeals = $memberMeals->sum('breakfast') + $memberMeals->sum('lunch') + $memberMeals->sum('dinner');
            
            $userTotalCost = $userTotalMeals * $mealRate;
            $userTotalDeposit = Deposit::where('mess_id', $messId)->where('user_id', $member->id)->whereMonth('date', $month)->whereYear('date', $year)->sum('amount');
            
            $balance = $userTotalDeposit - $userTotalCost;

            UserMonthlyBill::updateOrCreate(
                ['monthly_report_id' => $report->id, 'user_id' => $member->id],
                ['total_meals' => $userTotalMeals, 'total_cost' => $userTotalCost, 'total_deposit' => $userTotalDeposit, 'balance' => $balance]
            );
        }

        return response()->json(['status' => 'success', 'message' => 'Report generated successfully', 'data' => $report]);
    }
}
