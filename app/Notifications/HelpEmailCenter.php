<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class HelpEmailCenter extends Notification
{
    use Queueable;

    public string $name;
    public string $email;
    public string $bodyMessage;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $name, string $email, string $bodyMessage)
    {
        $this->name = $name;
        $this->email = $email;
        $this->bodyMessage = $bodyMessage;
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
            ->subject('Help Center Request from ' . $this->name)
            ->replyTo($this->email, $this->name)
            ->markdown('mail.help-email-center', [
                'name' => $this->name,
                'email' => $this->email,
                'bodyMessage' => $this->bodyMessage,
            ]);
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
