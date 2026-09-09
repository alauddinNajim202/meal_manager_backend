<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MealCountUpdatedNotification extends Notification
{
    use Queueable;

    protected $user;
    protected $date;
    protected $details;

    /**
     * Create a new notification instance.
     */
    public function __construct($user, $date, $details)
    {
        $this->user = $user;
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
        return ['database', \App\Channels\FirebaseChannel::class];
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
            'message' => "{$this->user->name} updated meals on {$formattedDate} for: {$this->details}",
            'category' => 'Meals',
            'icon' => 'utensils'
        ];
    }
}
