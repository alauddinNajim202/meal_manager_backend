<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BazarCostAddedNotification extends Notification
{
    use Queueable;

    protected $userName;
    protected $amount;
    protected $items;

    /**
     * Create a new notification instance.
     */
    public function __construct($userName, $amount, $items = null)
    {
        $this->userName = $userName;
        $this->amount = $amount;
        $this->items = $items;
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
        $message = "<b>{$this->userName}</b> added Bazar Cost of <b>৳{$this->amount}</b>";
        if ($this->items) {
            $message .= " (<i>{$this->items}</i>)";
        }
        $message .= ".";

        return [
            'title' => 'Bazar Cost Added',
            'message' => $message,
            'category' => 'Money & Cost',
            'icon' => 'shopping-bag'
        ];
    }

    /**
     * Get the push notification representation.
     */
    public function toFirebase(object $notifiable): array
    {
        $message = "{$this->userName} spent ৳{$this->amount} for Bazar";
        if ($this->items) {
            $message .= " ({$this->items}).";
        } else {
            $message .= ".";
        }

        return [
            'title' => '🛍️ New Bazar Expense',
            'message' => $message,
            'category' => 'Money & Cost',
            'icon' => 'shopping-bag'
        ];
    }
}
