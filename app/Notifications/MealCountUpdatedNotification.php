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
    protected $userName;
    protected $lunchCount;
    protected $dinnerCount;

    /**
     * Create a new notification instance.
     */
    public function __construct($date, $userName, $lunchCount, $dinnerCount)
    {
        $this->date = $date;
        $this->userName = $userName;
        $this->lunchCount = $lunchCount > 0 ? "+{$lunchCount}" : $lunchCount;
        $this->dinnerCount = $dinnerCount > 0 ? "+{$dinnerCount}" : $dinnerCount;
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
            'message' => "Manager updated meal for {$this->userName} on {$formattedDate} (Lunch: {$this->lunchCount}, Dinner: {$this->dinnerCount}).",
            'category' => 'Meals',
            'icon' => 'utensils'
        ];
    }
}
