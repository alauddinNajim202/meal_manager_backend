<?php

namespace App\Http\Controllers\Api;

use App\Events\TestNotificationEvent;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Notification;
use App\Notifications\TestNotification;
use Exception;
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

    public function index()
    {
        try {
            $user = auth('api')->user();
            
            // Get all notifications and decode the JSON 'data' column
            $notifications = DB::table('notifications')
                               ->orderBy('created_at', 'desc')
                               ->get()
                               ->map(function ($notification) {
                                   $data = is_string($notification->data) ? json_decode($notification->data, true) : $notification->data;
                                   
                                   return [
                                       'id'         => $notification->id, 
                                       'data'       => $data,
                                       'read_at'    => $notification->read_at,
                                       'created_at' => \Carbon\Carbon::parse($notification->created_at)->diffForHumans(['short' => true]),
                                       'updated_at' => \Carbon\Carbon::parse($notification->updated_at)->diffForHumans(['short' => true]),
                                   ];
                               });

            return response()->json([
                'status'     => true,
                'message'    => 'All Notifications',
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
            $notification = auth('api')->user()->notifications()->find($id);
            if($notification) {
                $notification->markAsRead();
            }
            return response()->json([
                'status'     => true,
                'message'    => 'Single Notification',
                'code'       => 200,
                'data'       => $notification
            ], 200);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return back();
        }
    }
    public function readAll()
    {
        try {
            auth('api')->user()->notifications->markAsRead();
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
