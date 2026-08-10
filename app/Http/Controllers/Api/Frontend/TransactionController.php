<?php

namespace App\Http\Controllers\Api\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\ApiResponse;
use App\Models\Deposit;
use App\Models\Expense;
use App\Models\User;

class TransactionController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $user = auth()->user();

        if (!$user->current_mess_id) {
            return $this->error(null, 'No active mess selected.', 400);
        }

        // Query Builder
        $depositQuery = Deposit::with('user')
            ->where('mess_id', $user->current_mess_id);

        $expenseQuery = Expense::with('user')
            ->where('mess_id', $user->current_mess_id);

        // Filter by year
        if ($request->filled('year')) {
            $depositQuery->whereYear('date', $request->year);
            $expenseQuery->whereYear('date', $request->year);
        }

        // Filter by month
        if ($request->filled('month')) {
            $depositQuery->whereMonth('date', $request->month);
            $expenseQuery->whereMonth('date', $request->month);
        }

        // Total
        $totalDeposits = $depositQuery->sum('amount');
        $totalExpenses = $expenseQuery->sum('amount');
        $netBalance = (string)($totalDeposits - $totalExpenses);


        // total used percentage

        $totalUsedPercentage = $totalDeposits > 0 ? ($totalExpenses / $totalDeposits) * 100 : 0;







        // Get records after applying filters
        $deposits = $depositQuery->get()->map(function ($deposit) {
            return [
                'id' => $deposit->id,
                'user_id' => $deposit->user_id,
                'user_name' => $deposit->user?->name,
                'amount' => $deposit->amount,
                'date' => $deposit->date,
                'note' => $deposit->note,
                'deposited_person' => $deposit->user?->name,
                'mess_id' => $deposit->mess_id,
                'created_by' => User::where('current_mess_id', $deposit->mess_id)
                    ->value('name'),
            ];
        });

        $expenses = $expenseQuery->get()->map(function ($expense) {
            return [
                'id' => $expense->id,
                'user_id' => $expense->user_id,
                'user_name' => $expense->user?->name,
                'amount' => $expense->amount,
                'date' => $expense->date,
                'note' => $expense->note,
                'created_by' => User::where('current_mess_id', $expense->mess_id)
                    ->value('name'),
            ];
        });

        $data = [
            'totalDeposits' => $totalDeposits,
            'totalExpenses' => $totalExpenses,
            'netBalance' => $netBalance,
            'totalUsedPercentage' => number_format($totalUsedPercentage, 2),
            'month' => $request->month,
            'year' => $request->year,
            'deposits' => $deposits,
            'expenses' => $expenses,
        ];

        return $this->success($data, 'Report fetched', 200);
    }
}
