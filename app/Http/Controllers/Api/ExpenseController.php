<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Expense;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = Expense::where('mess_id', $user->mess_id)->with('user:id,name');
        
        if ($request->has('month') && $request->has('year')) {
            $query->whereMonth('date', $request->month)->whereYear('date', $request->year);
        }

        return response()->json(['status' => 'success', 'data' => $query->latest('date')->get()]);
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:0',
            'date' => 'required|date',
            'description' => 'nullable|string'
        ]);

        $expense = Expense::create([
            'mess_id' => $user->mess_id,
            'user_id' => $request->user_id,
            'amount' => $request->amount,
            'date' => $request->date,
            'description' => $request->description
        ]);

        return response()->json(['status' => 'success', 'data' => $expense]);
    }

    public function destroy($id)
    {
        $expense = Expense::where('id', $id)->where('mess_id', auth()->user()->mess_id)->firstOrFail();
        $expense->delete();
        return response()->json(['status' => 'success', 'message' => 'Expense deleted']);
    }
}
