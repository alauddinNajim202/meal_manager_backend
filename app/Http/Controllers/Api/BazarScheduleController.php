<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\BazarSchedule;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Validator;

class BazarScheduleController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $user = auth()->user();

        if (!$user->current_mess_id) {
            return $this->error(null, 'No active mess selected.', 400);
        }
        
        $month = $request->input('month', date('m'));
        $year = $request->input('year', date('Y'));
        
        $mess = \App\Models\Mess::with('users:id,name')->find($user->current_mess_id);
        if (!$mess) {
            return $this->error(null, 'Mess not found.', 404);
        }

        $schedules = BazarSchedule::where('mess_id', $user->current_mess_id)
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->orderBy('date')
            ->get();

        $plannedDays = $schedules->count();
        $totalDaysInMonth = \Carbon\Carbon::createFromDate($year, $month, 1)->daysInMonth;

        $membersDuties = $mess->users->map(function ($member) use ($schedules) {
            $memberSchedules = $schedules->where('user_id', $member->id);
            
            $assignedDates = $memberSchedules->map(function ($schedule) {
                return [
                    'id' => $schedule->id,
                    'date' => $schedule->date,
                    'formatted_date' => \Carbon\Carbon::parse($schedule->date)->format('j M'),
                    'status' => $schedule->status
                ];
            })->values();

            $words = explode(' ', trim($member->name));
            $initials = '';
            foreach (array_slice($words, 0, 2) as $w) {
                if (!empty($w)) $initials .= strtoupper($w[0]);
            }

            return [
                'user_id' => $member->id,
                'name' => $member->name,
                'initials' => $initials,
                'days_assigned' => $memberSchedules->count(),
                'assigned_dates' => $assignedDates,
            ];
        })->values();

        $data = [
            'month' => date('F', mktime(0, 0, 0, $month, 10)),
            'year' => $year,
            'planned_days' => $plannedDays,
            'total_days' => $totalDaysInMonth,
            'members_duties' => $membersDuties
        ];

        return $this->success($data, 'Bazar schedule fetched successfully');
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        if (!$this->isManagerOfCurrentMess($user)) {
            return $this->error(null, 'Only managers can perform this action.', 403);
        }



        $validator = Validator::make($request->all(), [
            'schedules' => 'required|array|min:1',
            'schedules.*.user_id' => 'required|exists:users,id',
            'schedules.*.date' => 'required|date',
            // 'schedules.*.status' => 'in:pending,completed'
        ]);

        if ($validator->fails()) {
            return $this->error(
                $validator->errors()->first(),
                'Validation failed',
                422
            );
        }


    
        try {
            $messId = $user->current_mess_id;

            $saved = DB::transaction(function () use ($request, $messId) {
                $results = [];

                foreach ($request->schedules as $item) {
                    
                    $targetUser = User::find($item['user_id']);

                    if (!$targetUser) {
                        throw new \Exception('User not found.', 404);
                    }

                    // Verify user belongs to same mess
                    $belongsToMess = $targetUser
                        ->messes()
                        ->where('mess_id', $messId)
                        ->exists();

                    if (!$belongsToMess) {
                        throw new \Exception("User ID {$item['user_id']} does not belong to your mess.", 403);
                    }

                    $results[] = BazarSchedule::updateOrCreate(
                        ['mess_id' => $messId, 'date' => $item['date']],
                        ['user_id' => $item['user_id'], 'status' => $item['status'] ?? 'pending']
                    );
                }
                return $results;
            });

            return $this->success($saved, 'Bazar schedule created successfully');
        } catch (\Exception $e) {
            $code = in_array($e->getCode(), [403, 404]) ? $e->getCode() : 500;
            return $this->error($e->getMessage(), $code === 500 ? 'Something went wrong' : $e->getMessage(), $code);
        }

    }

    public function destroy(Request $request)
    {
        $user = auth()->user();

        if (!$this->isManagerOfCurrentMess($user)) {
            return $this->error(null, 'Only managers can perform this action.', 403);
        }

        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:bazar_schedules,id',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors()->first(), 'Validation failed', 422);
        }

        $schedule = BazarSchedule::where('id', $request->id)
            ->where('mess_id', $user->current_mess_id)
            ->first();

        if (!$schedule) {
            return $this->error(null, 'Schedule not found for this date.', 404);
        }

        $schedule->delete();

        return $this->success(null, 'Bazar schedule deleted successfully');
    }

    private function isManagerOfCurrentMess(User $user): bool
    {
        if (!$user->current_mess_id) return false;

        $pivot = $user->messes()->where('mess_id', $user->current_mess_id)->first();
        return $pivot && $pivot->pivot->role === 'manager';
    }
}
