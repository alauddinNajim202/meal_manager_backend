<?php

namespace App\Notifications;


use App\Helpers\Helper;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class NewPropertyNotification extends Notification
{
    use Queueable;

    public $data;

    /**
     * Create a new notification instance.
     */
    public function __construct($data)
    {
        $this->data = $data;
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
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->line('A new property has been posted.')
            ->action('View Property', url('/'))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
         $user = User::find($this->data['user_id']);
        if ($user->firebaseTokens) {
            $notifyData = [
                'title' => $this->data['title'],
                'body'  => $this->data['message'],
                'icon'  => config('settings.logo')
            ];
            foreach ($user->firebaseTokens as $firebaseToken) {
                Helper::sendNotifyMobile($firebaseToken->token, $notifyData);
            }
        }



        return [
            'type' => 'new_property',
            'title' => $this->data['title'],
            'body' => $this->data['body'],
            'property_id' => $this->data['property_id'],
        ];
    }
}
