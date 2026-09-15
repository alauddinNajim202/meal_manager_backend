<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Notice;
use App\Models\Mess;
use App\Traits\ApiResponse;
use App\Notifications\NewNoticeNotification;
use Illuminate\Support\Facades\Validator;
use Exception;

class NoticeController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $user = auth('api')->user();
        $messId = $user->current_mess_id;

        if (!$messId) {
            return $this->error(null, 'No active mess selected.', 400);
        }

        $notices = Notice::where('mess_id', $messId)
            ->with('creator:id,name,avatar')
            ->orderBy('created_at', 'desc')
            ->get();

        return $this->success($notices, 'Notices fetched successfully.', 200);
    }

    public function store(Request $request)
    {
        $user = auth('api')->user();
        $messId = $user->current_mess_id;

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'priority' => 'nullable|in:normal,urgent',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors()->first(), 'Validation failed', 422);
        }

        try {
            $notice = Notice::create([
                'mess_id' => $messId,
                'created_by' => $user->id,
                'title' => $request->title,
                'body' => $request->body,
                'priority' => $request->priority ?? 'normal',
            ]);

            // Notify mess members
            $mess = Mess::find($messId);
            if ($mess) {
                $mess->notify(new NewNoticeNotification($notice));
            }

            return $this->success($notice->load('creator:id,name,avatar'), 'Notice posted successfully.', 201);
        } catch (Exception $e) {
            return $this->error(null, $e->getMessage(), 500);
        }
    }

    public function update(Request $request, $id)
    {
        $user = auth('api')->user();
        
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'priority' => 'nullable|in:normal,urgent',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors()->first(), 'Validation failed', 422);
        }

        try {
            $notice = Notice::find($id);
            
            if (!$notice || $notice->mess_id != $user->current_mess_id) {
                return $this->error(null, 'Notice not found.', 404);
            }

            $notice->update([
                'title' => $request->title,
                'body' => $request->body,
                'priority' => $request->priority ?? 'normal',
            ]);

            return $this->success($notice->load('creator:id,name,avatar'), 'Notice updated successfully.', 200);
        } catch (Exception $e) {
            return $this->error(null, $e->getMessage(), 500);
        }
    }

    public function destroy($id)
    {
        $user = auth('api')->user();
        
        try {
            $notice = Notice::find($id);
            
            if (!$notice || $notice->mess_id != $user->current_mess_id) {
                return $this->error(null, 'Notice not found.', 404);
            }

            $notice->delete();

            return $this->success(null, 'Notice deleted successfully.', 200);
        } catch (Exception $e) {
            return $this->error(null, $e->getMessage(), 500);
        }
    }
}
