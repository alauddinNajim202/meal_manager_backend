<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use App\Channels\FirebaseChannel;

class AdminPushNotification extends Notification
{
    use Queueable;

    protected $title;
    protected $message;

    /**
     * Create a new notification instance.
     */
    public function __construct($title, $message)
    {
        $this->title = $title;
        $this->message = $message;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', FirebaseChannel::class];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => $this->title,
            'message' => $this->message,
            'category' => 'Admin',
            'icon' => 'bell'
        ];
    }

    /**
     * Get the push notification representation.
     */
    public function toFirebase(object $notifiable): array
    {
        return [
            'title' => $this->title,
            // Strip tags in case admin writes HTML for DB view
            'message' => strip_tags($this->message), 
            'category' => 'Admin',
            'icon' => 'bell'
        ];
    }
}
