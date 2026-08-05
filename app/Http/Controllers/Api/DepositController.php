<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Deposit;

class DepositController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = Deposit::where('mess_id', $user->mess_id)->with('user:id,name');
        
        if ($request->has('month') && $request->has('year')) {
            $query->whereMonth('date', $request->month)->whereYear('date', $request->year);
        }

        return response()->json(['status' => 'success', 'data' => $query->latest('date')->get()]);
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        if ($user->role !== 'manager') {
            return response()->json(['message' => 'Unauthorized. Only managers can add deposits.'], 403);
        }

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:0',
            'date' => 'required|date',
            'method' => 'nullable|string'
        ]);

        $deposit = Deposit::create([
            'mess_id' => $user->mess_id,
            'user_id' => $request->user_id,
            'amount' => $request->amount,
            'date' => $request->date,
            'method' => $request->method
        ]);

        return response()->json(['status' => 'success', 'data' => $deposit]);
    }

    public function destroy($id)
    {
        $deposit = Deposit::where('id', $id)->where('mess_id', auth()->user()->mess_id)->firstOrFail();
        $deposit->delete();
        return response()->json(['status' => 'success', 'message' => 'Deposit deleted']);
    }
}
