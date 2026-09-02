<?php

namespace App\Http\Controllers\Api;

use App\Events\TestNotificationEvent;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Notification;
use App\Notifications\TestNotification;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class NotificationController extends Controller
{
    public function test(){

        $user = auth('api')->user();
        $admin = User::role('admin', 'web')->first();

        $notiData = [
            'user_id' => $user->id,
            'title' => 'Test Notification Title.',
            'body' => 'Your Test Notification Body.',
            'icon'  => config('settings.logo')
        ];

        $admin->notify(new TestNotification($notiData, $admin->id));
        $user->notify(new TestNotification($notiData, $user->id));
        
        if(config('settings.reverb') == 'on'){
            broadcast(new TestNotificationEvent($notiData, $admin->id))->toOthers();
        }

        return true;
    }

    public function index(Request $request)
    {
        try {
            $user = auth('api')->user();
            $category = $request->input('category');
            
            if (!$user->current_mess_id) {
                return response()->json([
                    'status'     => true,
                    'message'    => 'No active mess',
                    'code'       => 200,
                    'data'       => [],
                ], 200);
            }
            
            $mess = \App\Models\Mess::find($user->current_mess_id);
            
            // Get notifications for the mess
            $query = $mess->notifications();
            
            $notifications = $query->get()->filter(function ($notification) use ($category) {
                // If a category is requested, filter by it. "All" means no filter.
                if ($category && strtolower($category) !== 'all') {
                    return isset($notification->data['category']) && $notification->data['category'] === $category;
                }
                return true;
            })->values()->map(function ($notification) {
                return [
                    'id'         => $notification->id, 
                    'data'       => $notification->data,
                    'read_at'    => $notification->read_at,
                    'created_at' => \Carbon\Carbon::parse($notification->created_at)->diffForHumans(['short' => true]),
                    'updated_at' => \Carbon\Carbon::parse($notification->updated_at)->diffForHumans(['short' => true]),
                ];
            });

            return response()->json([
                'status'     => true,
                'message'    => 'Notifications fetched successfully',
                'code'       => 200,
                'data'       => $notifications,
            ], 200);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return response()->json([
                'status'     => false,
                'message'    => 'Something went wrong',
                'code'       => 500,
                'error'      => $e->getMessage(),
            ], 500);
        }
    }




    public function readSingle($id)
    {
        try {
            $user = auth('api')->user();
            if ($user->current_mess_id) {
                $mess = \App\Models\Mess::find($user->current_mess_id);
                $notification = $mess->notifications()->find($id);
                if($notification) {
                    $notification->markAsRead();
                }
                return response()->json([
                    'status'     => true,
                    'message'    => 'Single Notification',
                    'code'       => 200,
                    'data'       => $notification
                ], 200);
            }
            return back();
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return back();
        }
    }
    public function readAll()
    {
        try {
            $user = auth('api')->user();
            if ($user->current_mess_id) {
                $mess = \App\Models\Mess::find($user->current_mess_id);
                $mess->unreadNotifications->markAsRead();
            }
            return response()->json([
                'status'     => true,
                'message'    => 'All Notifications Marked As Read',
                'code'       => 200,
                'data'       => null
            ], 200);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return back();
        }
    }

}
