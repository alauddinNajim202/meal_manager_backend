<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RolePromotedNotification extends Notification
{
    use Queueable;

    protected $promotedUserName;

    /**
     * Create a new notification instance.
     */
    public function __construct($promotedUserName)
    {
        $this->promotedUserName = $promotedUserName;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', \App\Channels\FirebaseChannel::class];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Role Promoted',
            'message' => "<b>{$this->promotedUserName}</b> was promoted to Mess Manager.",
            'category' => 'Members',
            'icon' => 'shield'
        ];
    }

    /**
     * Get the push notification representation.
     */
    public function toFirebase(object $notifiable): array
    {
        return [
            'title' => '👑 New Mess Manager',
            'message' => "{$this->promotedUserName} is now a Mess Manager! Expect new rules 😉",
            'category' => 'Members',
            'icon' => 'shield'
        ];
    }
}
