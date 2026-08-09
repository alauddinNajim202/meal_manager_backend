<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\Deposit;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class DepositController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $user = auth()->user();

        if (!$user->current_mess_id) {
            return $this->error(null, 'No active mess selected.', 400);
        }

        $query = Deposit::where('mess_id', $user->current_mess_id)->with('user:id,name,avatar');

        if ($request->has('month') && $request->has('year')) {
            $query->whereMonth('date', $request->month)->whereYear('date', $request->year);
        }

        return $this->success($query->latest('date')->get(), 'Deposits fetched successfully', 200);
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        if (!$user->current_mess_id) {
            return $this->error(null, 'No active mess selected.', 400);
        }

        // Only managers can add deposits — check via pivot table
        $pivotRole = $user->messes()->where('mess_id', $user->current_mess_id)->first()?->pivot->role;
        if ($pivotRole !== 'manager') {
            return $this->error(null, 'Only managers can add deposits.', 403);
        }

        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'amount'  => 'required|numeric|min:0',
            'date'    => 'required',
            'method'  => 'nullable|string',
            'note'    => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors()->first(), 'Validation failed', 422);
        }

        try {
            $deposit = Deposit::create([
                'mess_id' => $user->current_mess_id,
                'user_id' => $request->user_id,
                'amount'  => $request->amount,
                'date'    => $request->date,
                'method'  => $request->method,
                'note'    => $request->note,
            ]);

            return $this->success($deposit, 'Deposit added successfully', 201);

        } catch (Exception $e) {
            return $this->error(null, $e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function destroy($id)
    {
        $user = auth()->user();

        try {
            $deposit = Deposit::where('id', $id)
                ->where('mess_id', $user->current_mess_id)
                ->firstOrFail();

            $deposit->delete();

            return $this->success(null, 'Deposit deleted successfully', 200);

        } catch (Exception $e) {
            return $this->error(null, $e->getMessage(), $e->getCode() ?: 500);
        }
    }
}

