<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\Mess;
use App\Models\Meal;
use App\Models\Expense;
use App\Models\Deposit;
use App\Models\MonthlyReport;
use App\Models\UserMonthlyBill;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class ReportController extends Controller
{
    use ApiResponse;

    /**
     * Get the finalized monthly report for the current mess.
     */
    public function index(Request $request)
    {
        $user  = auth()->user();
        $month = $request->month ?? now()->month;
        $year  = $request->year  ?? now()->year;

        if (!$user->current_mess_id) {
            return $this->error(null, 'No active mess selected.', 400);
        }

        $report = MonthlyReport::where('mess_id', $user->current_mess_id)
            ->where('month', $month)
            ->where('year', $year)
            ->with('userMonthlyBills.user:id,name,avatar')
            ->first();

        return $this->success($report, 'Report fetched', 200);
    }

    /**
     * Get the authenticated user's personal bill for the month.
     */
    public function myBill(Request $request)
    {
        $user  = auth()->user();
        $month = $request->month ?? now()->month;
        $year  = $request->year  ?? now()->year;

        if (!$user->current_mess_id) {
            return $this->error(null, 'No active mess selected.', 400);
        }

        $report = MonthlyReport::where('mess_id', $user->current_mess_id)
            ->where('month', $month)->where('year', $year)->first();

        if (!$report) {
            return $this->error(null, 'Report not generated yet.', 404);
        }

        $myBill = UserMonthlyBill::where('monthly_report_id', $report->id)
            ->where('user_id', $user->id)->first();

        return $this->success($myBill, 'Your bill fetched', 200);
    }

    /**
     * Generate the monthly report for the current mess (managers only).
     */
    public function generate(Request $request)
    {
        $user = auth()->user();

        if (!$user->current_mess_id) {
            return $this->error(null, 'No active mess selected.', 400);
        }

        // Check if manager via pivot table
        $pivotRole = $user->messes()->where('mess_id', $user->current_mess_id)->first()?->pivot->role;
        if ($pivotRole !== 'manager') {
            return $this->error(null, 'Only managers can generate reports.', 403);
        }

        $validator = Validator::make($request->all(), [
            'month' => 'required|integer|min:1|max:12',
            'year'  => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors()->first(), 'Validation failed', 422);
        }

        try {
            $messId = $user->current_mess_id;
            $month  = $request->month;
            $year   = $request->year;

            // Collect all meals for this month
            $allMeals   = Meal::where('mess_id', $messId)->whereMonth('date', $month)->whereYear('date', $year)->get();
            $totalMeals = $allMeals->sum('breakfast') + $allMeals->sum('lunch') + $allMeals->sum('dinner');

            $totalCost = Expense::where('mess_id', $messId)->whereMonth('date', $month)->whereYear('date', $year)->sum('amount');

            $mealRate = $totalMeals > 0 ? round($totalCost / $totalMeals, 4) : 0;

            $report = MonthlyReport::updateOrCreate(
                ['mess_id' => $messId, 'month' => $month, 'year' => $year],
                ['total_meals' => $totalMeals, 'total_cost' => $totalCost, 'meal_rate' => $mealRate, 'status' => 'finalized']
            );

            // Generate individual bills for each member via pivot
            $members = Mess::find($messId)->users()->get();

            foreach ($members as $member) {
                $memberMeals    = $allMeals->where('user_id', $member->id);
                $userTotalMeals = $memberMeals->sum('breakfast') + $memberMeals->sum('lunch') + $memberMeals->sum('dinner');
                $userTotalCost  = round($userTotalMeals * $mealRate, 2);
                $userDeposit    = Deposit::where('mess_id', $messId)->where('user_id', $member->id)
                    ->whereMonth('date', $month)->whereYear('date', $year)->sum('amount');
                $balance = round($userDeposit - $userTotalCost, 2);

                UserMonthlyBill::updateOrCreate(
                    ['monthly_report_id' => $report->id, 'user_id' => $member->id],
                    ['total_meals' => $userTotalMeals, 'total_cost' => $userTotalCost, 'total_deposit' => $userDeposit, 'balance' => $balance]
                );
            }

            return $this->success($report->load('userMonthlyBills.user:id,name'), 'Report generated successfully', 200);

        } catch (Exception $e) {
            return $this->error(null, $e->getMessage(), $e->getCode() ?: 500);
        }
    }
}

