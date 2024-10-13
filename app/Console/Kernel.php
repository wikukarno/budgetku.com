<?php

namespace App\Console;

use App\Mail\BillMail;
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

            $financeCounts = Finance::whereIn('users_id', [1, 8])
                ->whereDate('created_at', Carbon::today())
                ->get()
                ->groupBy('users_id')
                ->map(fn($finance) => $finance->count());

            foreach ($users as $user) {
                $count = $financeCounts->get($user->id, 0); // Default to 0 if not found

                if ($count === 0) {
                    Mail::to($user->email)->send(new ExpenseNotificationEmptyMail($user));
                }
            }
        })->dailyAt('18:00')->timezone('Asia/Jakarta');
    }

    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');
        require base_path('routes/console.php');
    }
}
