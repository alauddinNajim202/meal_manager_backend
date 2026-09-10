<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MonthClosedNotification extends Notification
{
    use Queueable;

    protected $monthName;
    protected $year;
    protected $mealRate;

    /**
     * Create a new notification instance.
     */
    public function __construct($monthName, $year, $mealRate)
    {
        $this->monthName = $monthName;
        $this->year = $year;
        $this->mealRate = $mealRate;
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
            'title' => "Month Closed ({$this->monthName} {$this->year})",
            'message' => "<b>{$this->monthName}</b> month settlement completed! Meal rate: <b>৳{$this->mealRate}</b>. Check your final balance.",
            'category' => 'Month End',
            'icon' => 'refresh-cw'
        ];
    }

    /**
     * Get the push notification representation.
     */
    public function toFirebase(object $notifiable): array
    {
        return [
            'title' => "📊 Month End: {$this->monthName} {$this->year}",
            'message' => "Settlement done! Final meal rate is ৳{$this->mealRate}. Tap to view your balance.",
            'category' => 'Month End',
            'icon' => 'refresh-cw'
        ];
    }
}
