<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\Expense;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

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

        $month = $request->month ?? \Carbon\Carbon::now()->month;
        $year = $request->year ?? \Carbon\Carbon::now()->year;

        $query->whereMonth('date', $month)->whereYear('date', $year);

        return $this->success($query->latest('date')->get(), 'Expenses fetched successfully', 200);
    }

    public function store(Request $request)
    {
        $user = auth('api')->user();

        if (!$user->current_mess_id) {
            return $this->error(null, 'No active mess selected.', 400);
        }

        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'reason'      => 'required|string',
            'amount'      => 'required|numeric|min:0',
            'date'        => 'required|string',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->error(null, $validator->errors()->first(), 422);
        }

        if (!$this->isManagerOfCurrentMess($user)) {
            return $this->error(null, 'You are not authorized to perform this action.', 403);
        }

        try {
            $expense = Expense::create([
                'mess_id'     => $user->current_mess_id,
                'user_id'     => $request->user_id,
                'amount'      => $request->amount,
                'reason'      => $request->reason,
                'date'        => $request->date ?? now(),
                'description' => $request->description,
            ]);

            // Notify the mess
            $mess = \App\Models\Mess::find($user->current_mess_id);
            $targetUser = \App\Models\User::find($request->user_id);
            
            if ($mess && $targetUser) {
                $mess->notify(new \App\Notifications\BazarCostAddedNotification($targetUser->name, $request->amount, $request->description));
            }

            return $this->success($expense, 'Expense added successfully', 201);

        } catch (Exception $e) {
            return $this->error(null, $e->getMessage(), 500);
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

    private function isManagerOfCurrentMess(User $user): bool
    {
        if (!$user->current_mess_id) return false;

        $pivot = $user->messes()->where('mess_id', $user->current_mess_id)->first();
        return $pivot && $pivot->pivot->role === 'manager';
    }
}

