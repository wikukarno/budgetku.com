<?php

namespace App\Mail;

use App\Models\Salary;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UangMasuk extends Mailable
{
    use Queueable, SerializesModels;

    public $salary;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Salary $salary)
    {
        $this->salary = $salary;
    }

    public function build()
    {
        return $this->markdown('emails.uang-masuk')
        ->subject('Notifikasi Uang Masuk');
    }
}
