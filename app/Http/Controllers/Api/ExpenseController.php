<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\Expense;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class ExpenseController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $user = auth()->user();

        if (!$user->current_mess_id) {
            return $this->error(null, 'No active mess selected.', 400);
        }

        $query = Expense::where('mess_id', $user->current_mess_id)->with('user:id,name,avatar');

        if ($request->has('month') && $request->has('year')) {
            $query->whereMonth('date', $request->month)->whereYear('date', $request->year);
        }

        return $this->success($query->latest('date')->get(), 'Expenses fetched successfully', 200);
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        if (!$user->current_mess_id) {
            return $this->error(null, 'No active mess selected.', 400);
        }

        $validator = Validator::make($request->all(), [
            'user_id'     => 'required|exists:users,id',
            'amount'      => 'required|numeric|min:0',
            'date'        => 'required|date',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors()->first(), 'Validation failed', 422);
        }

        try {
            $expense = Expense::create([
                'mess_id'     => $user->current_mess_id,
                'user_id'     => $request->user_id,
                'amount'      => $request->amount,
                'date'        => $request->date,
                'description' => $request->description,
            ]);

            return $this->success($expense, 'Expense added successfully', 201);

        } catch (Exception $e) {
            return $this->error(null, $e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function destroy($id)
    {
        $user = auth()->user();

        try {
            $expense = Expense::where('id', $id)
                ->where('mess_id', $user->current_mess_id)
                ->firstOrFail();

            $expense->delete();

            return $this->success(null, 'Expense deleted successfully', 200);

        } catch (Exception $e) {
            return $this->error(null, $e->getMessage(), $e->getCode() ?: 500);
        }
    }
}

