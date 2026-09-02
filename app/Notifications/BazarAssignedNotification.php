<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BazarAssignedNotification extends Notification
{
    use Queueable;

    protected $date;
    protected $userName;
    protected $partnerName;

    /**
     * Create a new notification instance.
     */
    public function __construct($date, $userName, $partnerName = null)
    {
        $this->date = $date;
        $this->userName = $userName;
        $this->partnerName = $partnerName;
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
        $formattedDate = \Carbon\Carbon::parse($this->date)->isTomorrow() 
            ? "tomorrow's" 
            : \Carbon\Carbon::parse($this->date)->format('M d') . "'s";
            
        $message = "{$this->userName} is scheduled for {$formattedDate} Bazar duty";
        if ($this->partnerName) {
            $message .= " with {$this->partnerName}";
        }
        $message .= ".";

        return [
            'title' => 'Bazar Assigned',
            'message' => $message,
            'category' => 'Bazar',
            'icon' => 'shopping-cart'
        ];
    }
}
