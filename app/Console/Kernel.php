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
                return Carbon::parse($date)->subDay(2)->format('d');
            });

            $uniqueDates = $billDates->unique();

            // Mendapatkan tanggal hari ini
            $today = Carbon::now()->format('d');

            foreach ($uniqueDates as $date) {
                try {
                    if ($date == $today) {

                        $dueDate = date('d', strtotime('+2 days'));

                        // Ambil semua tagihan yang jatuh tempo pada tanggal tersebut
                        $billsDueTomorrow = Bill::with('user')
                            ->where('siklus_tagihan', 0)
                            ->whereDay('jatuh_tempo_tagihan', $dueDate)
                            ->get();

                        if ($billsDueTomorrow->isNotEmpty()) {
                            // Kirim email
                            Mail::to('prasetyagama2@gmail.com')->send(new BillMail($billsDueTomorrow));
                        }
                    }
                } catch (\Throwable $th) {
                    Log::error($th);
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
