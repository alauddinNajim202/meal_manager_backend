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
        if (!in_array($pivotRole, ['manager', 'owner'])) {
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
            
            // Dispatch notifications to the mess
            $monthName = \Carbon\Carbon::create()->month($month)->format('F');
            $nextMonthDate = \Carbon\Carbon::createFromDate($year, $month, 1)->addMonth();
            $nextMonthName = $nextMonthDate->format('F');
            $nextMonthYear = $nextMonthDate->format('Y');

            $mess = \App\Models\Mess::find($messId);
            if ($mess) {
                $mess->notify(new \App\Notifications\MonthClosedNotification($monthName, $year, $mealRate));
                $mess->notify(new \App\Notifications\NewMonthStartedNotification($nextMonthName, $nextMonthYear));
            }

            return $this->success($report->load('userMonthlyBills.user:id,name'), 'Report generated successfully', 200);

        } catch (Exception $e) {
            return $this->error(null, $e->getMessage(), $e->getCode() ?: 500);
        }
    }

    /**
     * Get the last month's finalized report and member bills.
     */
    public function lastMonth(Request $request)
    {
        $user = auth()->user();

        if (!$user->current_mess_id) {
            return $this->error(null, 'No active mess selected.', 400);
        }

        $messId = $user->current_mess_id;
        $currentMonth = $request->month ?? now()->month;
        $currentYear  = $request->year ?? now()->year;

        $lastMonthDate = \Carbon\Carbon::createFromDate($currentYear, $currentMonth, 1)->subMonth();
        $targetMonth = $lastMonthDate->month;
        $targetYear  = $lastMonthDate->year;

        $report = MonthlyReport::where('mess_id', $messId)
            ->where('month', $targetMonth)
            ->where('year', $targetYear)
            ->with(['userMonthlyBills.user:id,name,avatar'])
            ->first();

        if (!$report) {
            $meals = Meal::where('mess_id', $messId)->whereMonth('date', $targetMonth)->whereYear('date', $targetYear)->get();
            $totalMeals = $meals->sum('breakfast') + $meals->sum('lunch') + $meals->sum('dinner');
            $totalCost = (float) Expense::where('mess_id', $messId)->whereMonth('date', $targetMonth)->whereYear('date', $targetYear)->sum('amount');
            $totalDeposits = (float) Deposit::where('mess_id', $messId)->whereMonth('date', $targetMonth)->whereYear('date', $targetYear)->sum('amount');
            $mealRate = $totalMeals > 0 ? round($totalCost / $totalMeals, 2) : 0;

            return $this->success([
                'month'               => $targetMonth,
                'month_name'          => $lastMonthDate->format('F'),
                'year'                => $targetYear,
                'is_finalized'        => false,
                'total_meals'         => $totalMeals,
                'total_cost'          => $totalCost,
                'total_deposits'      => $totalDeposits,
                'meal_rate'           => $mealRate,
                'user_monthly_bills'  => [],
            ], 'Last month unfinalized summary fetched', 200);
        }

        $myBill = $report->userMonthlyBills->where('user_id', $user->id)->first();

        return $this->success([
            'month'              => $targetMonth,
            'month_name'         => $lastMonthDate->format('F'),
            'year'               => $targetYear,
            'is_finalized'       => true,
            'report_id'          => $report->id,
            'total_meals'        => (float) $report->total_meals,
            'total_cost'         => (float) $report->total_cost,
            'meal_rate'          => (float) $report->meal_rate,
            'status'             => $report->status,
            'my_bill'            => $myBill,
            'user_monthly_bills' => $report->userMonthlyBills,
        ], 'Last month report fetched successfully', 200);
    }

    /**
     * Pass last month's closing balances into the current month as opening balance deposits.
     * Managers only.
     */
    public function carryForward(Request $request)
    {
        $user = auth()->user();

        if (!$user->current_mess_id) {
            return $this->error(null, 'No active mess selected.', 400);
        }

        $pivotRole = $user->messes()->where('mess_id', $user->current_mess_id)->first()?->pivot->role;
        if (!in_array($pivotRole, ['manager', 'owner'])) {
            return $this->error(null, 'Only managers can carry forward balances.', 403);
        }

        $validator = Validator::make($request->all(), [
            'from_month' => 'required|integer|min:1|max:12',
            'from_year'  => 'required|integer',
            'to_month'   => 'required|integer|min:1|max:12',
            'to_year'    => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors()->first(), 'Validation failed', 422);
        }

        $messId = $user->current_mess_id;
        $report = MonthlyReport::where('mess_id', $messId)
            ->where('month', $request->from_month)
            ->where('year', $request->from_year)
            ->with('userMonthlyBills')
            ->first();

        if (!$report) {
            return $this->error(null, 'From month report has not been finalized yet.', 404);
        }

        $toStartDate = \Carbon\Carbon::createFromDate($request->to_year, $request->to_month, 1)->format('Y-m-d');
        $passedCount = 0;

        foreach ($report->userMonthlyBills as $bill) {
            if ($bill->balance != 0) {
                Deposit::updateOrCreate(
                    [
                        'mess_id' => $messId,
                        'user_id' => $bill->user_id,
                        'date'    => $toStartDate,
                        'note'    => "Carried over from {$request->from_month}/{$request->from_year}",
                    ],
                    [
                        'amount' => $bill->balance,
                        'method' => 'carry_forward',
                    ]
                );
                $passedCount++;
            }
        }

        return $this->success([
            'carried_members_count' => $passedCount,
            'from_month' => $request->from_month,
            'to_month'   => $request->to_month,
        ], "Balances carried forward to {$request->to_month}/{$request->to_year} successfully.", 200);
    }
}

