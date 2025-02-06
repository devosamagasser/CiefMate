<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SendInvitation extends Notification
{
    use Queueable;

    public $invitationId;
    /**
     * Create a new notification instance.
     */
    public function __construct($invitationId)
    {
        $this->invitationId = $invitationId;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
        ->subject('Chef Mate: Invitation to my workspace')
        ->greeting("Hello!, {$notifiable->name}")
        ->line('join my workspace by visit invitation link ')
        ->action('Invitation Link', route('accept.invitation',$this->invitationId))
        ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
