<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MoneyAddedNotification extends Notification
{
    use Queueable;

    protected $userName;
    protected $amount;

    /**
     * Create a new notification instance.
     */
    public function __construct($userName, $amount)
    {
        $this->userName = $userName;
        $this->amount = $amount;
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
            'title' => 'Money Added',
            'message' => "{$this->userName} deposited ৳{$this->amount} to mess fund.",
            'category' => 'Money & Cost',
            'icon' => 'wallet'
        ];
    }
}
