<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewMonthStartedNotification extends Notification
{
    use Queueable;

    protected $monthName;
    protected $year;

    /**
     * Create a new notification instance.
     */
    public function __construct($monthName, $year)
    {
        $this->monthName = $monthName;
        $this->year = $year;
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
            'title' => 'New Month Started',
            'message' => "<b>{$this->monthName} {$this->year}</b> month cycle is now active. Add initial deposits.",
            'category' => 'Month End',
            'icon' => 'calendar'
        ];
    }

    /**
     * Get the push notification representation.
     */
    public function toFirebase(object $notifiable): array
    {
        return [
            'title' => "📅 {$this->monthName} {$this->year} Started",
            'message' => "A new mess month has begun. Please submit your initial deposits.",
            'category' => 'Month End',
            'icon' => 'calendar'
        ];
    }
}
