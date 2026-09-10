<?php

namespace App\Http\Controllers\Web\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Notifications\AdminPushNotification;

class PushNotificationController extends Controller
{
    public function index()
    {
        $users = User::select('id', 'name', 'email')->get();
        return view('backend.layouts.push_notification.index', compact('users'));
    }

    public function send(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'user_ids' => 'required|array',
            'user_ids.*' => 'in:all|exists:users,id'
        ]);

        $title = $request->title;
        $message = $request->message;
        $userIds = $request->user_ids;

        if (in_array('all', $userIds)) {
            $users = User::all();
        } else {
            $users = User::whereIn('id', $userIds)->get();
        }

        foreach ($users as $user) {
            $user->notify(new AdminPushNotification($title, $message));
        }

        return redirect()->back()->with('t-success', 'Push notification sent successfully!');
    }
}
