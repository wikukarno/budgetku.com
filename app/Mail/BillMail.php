<?php

namespace App\Mail;

use App\Models\Bill;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BillMail extends Mailable
{
    use Queueable, SerializesModels;

    public $bills; // Ganti nama variabel untuk lebih jelas

    public function __construct($bills) // Tipe data tidak perlu array
    {
        $this->bills = $bills;
    }

    public function build()
    {
        $due_date = Carbon::now()->addDay(2)->format('Y-m-d'); // Mendapatkan tanggal untuk hari berikutnya
        return $this->markdown('emails.bill')
        ->subject('Tagihan')
        ->with([
            'bills' => $this->bills,
            'due_date' => $due_date,
        ]);
    }
}
