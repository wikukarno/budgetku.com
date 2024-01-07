<?php

namespace App\Mail;

use App\Models\Finance;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UangKeluar extends Mailable
{
    use Queueable, SerializesModels;

    public $finance;

    /**
     * Create a new message instance.
     *
     * @param  Finance  $finance
     * @return void
     */
    public function __construct(Finance $finance)
    {
        $this->finance = $finance;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.uang-keluar')
            ->subject('Notifikasi Uang Keluar');
    }
}
