<?php

namespace App\Jobs;

use App\Mail\UangKeluar;
use App\Models\Finance;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ProcessUangKeluarEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    // get data
    protected $data;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(array $data)
    {
        $this->data = $data; // set user
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // get finance data by user id
        $user = $this->data['user'];
        $finance = $this->data['finance'];
        $saldo = $this->data['saldo'];

        // Cek apakah notifikasi user utama aktif
        if ($user->notifications != 1) {
            Log::info('Notifications are disabled for the main user. No email sent.');
            return false;
        }

        // Kirim email ke user utama
        if ($finance) {
            Mail::to($user->email)->send(new UangKeluar($finance, $saldo));

            // Cek apakah ada email_parrent
            if ($user->email_parrent) {
                // Memisahkan email_parrent berdasarkan koma
                $emailParents = explode(',', $user->email_parrent);

                // Mengirim email ke setiap email orang tua
                foreach ($emailParents as $parentEmail) {
                    Mail::to($parentEmail)->send(new UangKeluar($finance, $saldo));
                }
            } else {
                Log::info('No parent emails found.');
            }
        } else {
            Log::info('Finance data not found. No email sent.');
            return false;
        }
    }
}
