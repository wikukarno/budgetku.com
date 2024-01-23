<?php

namespace App\Console;

use App\Mail\BillMail;
use App\Models\Bill;
use Carbon\Carbon;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule)
    {
        // $billBulanan = Bill::with('user')
        //     ->where('siklus_tagihan', 0)
        //     ->whereDate('jatuh_tempo_tagihan', Carbon::now()->addDays(1)->toDateString())
        //     ->get();

        // foreach ($billBulanan as $bill) {
        //     $schedule->call(function () use ($bill) {
        //         Log::info('Sending email to ' . 'prasetyagama2@gmail.com');
        //         Mail::to('prasetyagama2@gmail.com')->send(new BillMail($bill));
        //     })->everyMinute();
        // }

        $schedule->call(function () {
            $billsDueTomorrow = Bill::with('user')
            ->where('siklus_tagihan', 0)
            ->whereDate('jatuh_tempo_tagihan', Carbon::now()->addDay(1)->toDateString())
                ->get();

            foreach ($billsDueTomorrow as $bill) {
                Log::info('Sending email to prasetyagama2@gmail.com');
                Mail::to('prasetyagama2@gmail.com')->send(new BillMail($bill));
            }
        })->everyMinute();
    }

    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');
        require base_path('routes/console.php');
    }
}
