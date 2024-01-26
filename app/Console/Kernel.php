<?php

namespace App\Console;

use App\Mail\BillMail;
use App\Models\Bill;
use Carbon\Carbon;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule)
    {

        $schedule->call(function () {

            $bills = Bill::select('jatuh_tempo_tagihan')
            ->where('siklus_tagihan', 0)
            ->get();

            $billDates = $bills->pluck('jatuh_tempo_tagihan')->map(function ($date) {
                // Mengurangi satu hari dari tanggal jatuh tempo
                return Carbon::parse($date)->subDay()->format('Y-m-d');
            });

            $uniqueDates = $billDates->unique();

            // Mendapatkan tanggal hari ini
            $today = Carbon::now()->format('Y-m-d');

            foreach ($uniqueDates as $date) {
                if ($date == $today) {
                    $dueDate = Carbon::parse($date)->addDay()->format('Y-m-d');

                    // Ambil semua tagihan yang jatuh tempo pada tanggal tersebut
                    $billsDueTomorrow = Bill::with('user')
                        ->where('siklus_tagihan', 0)
                        ->whereDate('jatuh_tempo_tagihan', $dueDate)
                        ->get();

                    if ($billsDueTomorrow->isNotEmpty()) {
                        // kirim log
                        Log::info('Sending email to prasetyagama2@gmail.com' . $dueDate);
                        // Kirim email
                        Mail::to('prasetyagama2@gmail.com')->send(new BillMail($billsDueTomorrow));
                    }else{
                        Log::info('Tidak ada tagihan yang jatuh tempo pada tanggal ' . $dueDate);
                    }
                }
            }

        })->dailyAt('07:00')->timezone('Asia/Jakarta');
    }

    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');
        require base_path('routes/console.php');
    }
}
