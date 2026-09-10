<?php

namespace App\Channels;

use Illuminate\Notifications\Notification;
use App\Models\Mess;
use App\Models\User;
use App\Helpers\Helper;

class FirebaseChannel
{
    /**
     * Send the given notification.
     *
     * @param  object  $notifiable
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return void
     */
    public function send(object $notifiable, Notification $notification): void
    {
        if (method_exists($notification, 'toFirebase')) {
            $data = $notification->toFirebase($notifiable);
        } elseif (method_exists($notification, 'toArray')) {
            $data = $notification->toArray($notifiable);
        } else {
            return;
        }
        
        // Ensure there's actually a message to send
        if (empty($data['message']) && empty($data['title'])) {
            return;
        }

        $notifyData = [
            'title' => $data['title'] ?? 'New Notification',
            'body'  => $data['message'] ?? '',
            'icon'  => $data['icon'] ?? config('settings.logo', '')
        ];

        if ($notifiable instanceof Mess) {
            // Get all active users of this mess
            $users = $notifiable->users()->with('firebaseTokens')->get();
            foreach ($users as $user) {
                if ($user->firebaseTokens) {
                    foreach ($user->firebaseTokens as $tokenModel) {
                        if (!empty($tokenModel->token)) {
                            Helper::sendNotifyMobile($tokenModel->token, $notifyData);
                        }
                    }
                }
            }
        } elseif ($notifiable instanceof User) {
            if ($notifiable->firebaseTokens) {
                foreach ($notifiable->firebaseTokens as $tokenModel) {
                    if (!empty($tokenModel->token)) {
                        Helper::sendNotifyMobile($tokenModel->token, $notifyData);
                    }
                }
            }
        }
    }
}
