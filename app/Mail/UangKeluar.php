<?php

namespace App\Mail;

use App\Models\Finance;
use App\Models\Salary;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UangKeluar extends Mailable
{
    use Queueable, SerializesModels;

    public $finance;
    public $saldo;

    /**
     * Create a new message instance.
     *
     * @param  Finance  $finance
     * @return void
     */
    public function __construct(Finance $finance, float $saldo)
    {
        $this->finance = $finance;
        $this->saldo = $saldo;
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
