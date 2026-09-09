<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewMemberJoinedNotification extends Notification
{
    use Queueable;

    protected $memberName;

    /**
     * Create a new notification instance.
     */
    public function __construct($memberName)
    {
        $this->memberName = $memberName;
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
            'title' => 'New Member Joined',
            'message' => "<b>{$this->memberName}</b> has joined the mess.",
            'category' => 'Members',
            'icon' => 'user-plus'
        ];
    }
}
