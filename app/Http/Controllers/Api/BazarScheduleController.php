<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\BazarSchedule;

class BazarScheduleController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = BazarSchedule::where('mess_id', $user->mess_id)->with('user:id,name');
        
        if ($request->has('month') && $request->has('year')) {
            $query->whereMonth('date', $request->month)->whereYear('date', $request->year);
        }

        return response()->json(['status' => 'success', 'data' => $query->orderBy('date')->get()]);
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'date' => 'required|date',
            'status' => 'in:pending,completed'
        ]);

        $schedule = BazarSchedule::updateOrCreate(
            ['mess_id' => $user->mess_id, 'date' => $request->date],
            ['user_id' => $request->user_id, 'status' => $request->status ?? 'pending']
        );

        return response()->json(['status' => 'success', 'data' => $schedule]);
    }
}
