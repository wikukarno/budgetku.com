<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ExpenseNotificationEmptyMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(protected User $user)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Expense Notification Empty',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.expense-notification-empty',
            with: [
                'user' => $this->user,
                'url' => config('app.url'),
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
