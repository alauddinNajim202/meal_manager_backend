<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MealCountUpdatedNotification extends Notification
{
    use Queueable;

    protected $date;
    protected $details;

    /**
     * Create a new notification instance.
     */
    public function __construct($date, $details)
    {
        $this->date = $date;
        $this->details = $details;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $formattedDate = \Carbon\Carbon::parse($this->date)->format('M d');
        return [
            'title' => 'Meal Count Updated',
            'message' => "Manager updated meals on {$formattedDate} for: {$this->details}",
            'category' => 'Meals',
            'icon' => 'utensils'
        ];
    }
}
