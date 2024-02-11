<?php

namespace App\Mail;

use App\Models\Bill;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

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

        $due_date = Carbon::parse($this->bills->first()->jatuh_tempo_tagihan)->isoFormat('dddd, D MMMM Y');
        
        $user = $this->bills->first()->user; // Mengambil user pertama dari tagihan
        return $this->markdown('emails.bill')
            ->subject('Tagihan')
            ->with([
                'bills' => $this->bills,
                'due_date' => $due_date, // Mengirim tanggal jatuh tempo ke view
                'user' => $user
            ]);
    }
}
