<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class NewNoticeNotification extends Notification
{
    use Queueable;

    protected $notice;

    public function __construct($notice)
    {
        $this->notice = $notice;
    }

    public function via(object $notifiable): array
    {
        return ['database', \App\Channels\FirebaseChannel::class];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => $this->notice->priority === 'urgent' ? '🚨 Urgent Notice' : '📢 New Notice',
            'message' => $this->notice->title,
            'category' => 'Notice',
            'icon' => 'bell'
        ];
    }

    public function toFirebase(object $notifiable): array
    {
        return [
            'title' => $this->notice->priority === 'urgent' ? '🚨 Urgent Notice' : '📢 New Notice',
            'message' => $this->notice->title,
            'category' => 'Notice',
            'icon' => 'bell'
        ];
    }
}
