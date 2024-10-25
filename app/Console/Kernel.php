<?php

namespace App\Console;

use App\Mail\BillMail;
use App\Mail\ExampleNotificationMail;
use App\Mail\ExpenseNotificationEmptyMail;
use App\Models\Bill;
use App\Models\Finance;
use App\Models\User;
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
            $users = User::whereIn('id', [1, 8])->get();

            // Ambil semua transaksi finance yang dibuat hari ini oleh user id 1 dan 8
            $financeCounts = Finance::whereIn('users_id', [1, 8]) // hanya untuk user id 1 dan 8
            ->whereDate('purchase_date', Carbon::today())
            ->get()
            ->groupBy('users_id')
            ->map(fn($finance) => $finance->count());

            // Loop untuk mengirim email jika user tidak memiliki transaksi hari ini
            foreach ($users as $user) {
                // Ambil jumlah transaksi user, default ke 0 jika tidak ada
                $count = $financeCounts->get($user->id, 0);

                // Jika user tidak memiliki transaksi, kirim email
                if ($count == 0) {
                    Mail::to('prasetyagama2@gmail.com')->send(new ExpenseNotificationEmptyMail($user));
                }
            }
        })
        ->dailyAt('18:00')->timezone('Asia/Jakarta');
    }

    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');
        require base_path('routes/console.php');
    }
}
