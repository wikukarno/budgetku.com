<?php

namespace App\Console;

use App\Models\User;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Request;
use Symfony\Component\Mailer\Messenger\SendEmailMessage;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {

        $user = User::all();
        $schedule->command(
            SendEmailsCommand::class,
            [
                'from' => env('MAIL_FROM_ADDRESS'),
                'to' => $user->email,
                'subject' => 'Test',
                'body' => 'Test'
            ]
        )->everyMinute();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
