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
        return ['database', \App\Channels\FirebaseChannel::class];
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
            
        $message = "<b>{$this->userName}</b> is scheduled for <b>{$formattedDate}</b> Bazar duty";
        if ($this->partnerName) {
            $message .= " with <b>{$this->partnerName}</b>";
        }
        $message .= ".";

        return [
            'title' => 'Bazar Assigned',
            'message' => $message,
            'category' => 'Bazar',
            'icon' => 'shopping-cart'
        ];
    }

    /**
     * Get the push notification representation.
     */
    public function toFirebase(object $notifiable): array
    {
        $formattedDate = \Carbon\Carbon::parse($this->date)->isTomorrow() 
            ? "Tomorrow" 
            : \Carbon\Carbon::parse($this->date)->format('M d');
            
        $message = "Hey {$this->userName}, you're on Bazar duty {$formattedDate}!";
        if ($this->partnerName) {
            $message .= " (Partner: {$this->partnerName})";
        }

        return [
            'title' => '🛒 Bazar Duty Alert',
            'message' => $message,
            'category' => 'Bazar',
            'icon' => 'shopping-cart'
        ];
    }
}
