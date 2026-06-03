<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AgencyCredentialsNotification extends Notification
{
    use Queueable;

    public $temporaryPassword;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $temporaryPassword)
    {
        $this->temporaryPassword = $temporaryPassword;
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
                    ->subject('Agency Account Created - MySebenarnya Portal')
                    ->greeting('Hello ' . $notifiable->name . ',')
                    ->line('Your agency account has been created for the MySebenarnya Portal.')
                    ->line('Your login credentials are:')
                    ->line('**Email:** ' . $notifiable->email)
                    ->line('**Temporary Password:** ' . $this->temporaryPassword)
                    ->line('For security reasons, you will be required to change your password upon first login.')
                    ->action('Login to Portal', url('/login/agency'))
                    ->line('If you have any questions, please contact our support team.')
                    ->line('Thank you for using MySebenarnya Portal!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'temporary_password_sent' => true,
            'sent_at' => now(),
        ];
    }
}
