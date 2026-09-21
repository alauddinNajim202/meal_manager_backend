<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewPollCreatedNotification extends Notification
{
    use Queueable;

    protected $pollTitle;

    /**
     * Create a new notification instance.
     */
    public function __construct($pollTitle)
    {
        $this->pollTitle = $pollTitle;
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
            'title' => 'New Poll Created',
            'message' => "A new poll '<b>{$this->pollTitle}</b>' has been created in your mess. Please vote!",
            'category' => 'Polls',
            'icon' => 'pie-chart'
        ];
    }

    /**
     * Get the push notification representation.
     */
    public function toFirebase(object $notifiable): array
    {
        return [
            'title' => '📊 New Poll Created!',
            'message' => "A new poll '{$this->pollTitle}' has been created in your mess. Please vote!",
            'category' => 'Polls',
            'icon' => 'pie-chart'
        ];
    }
}
