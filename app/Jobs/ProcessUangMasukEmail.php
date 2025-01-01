<?php

namespace App\Jobs;

use App\Models\Finance;
use App\Mail\UangKeluar;
use App\Mail\UangMasuk;
use App\Models\Salary;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ProcessUangMasukEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    // get user
    protected $data;

    /**
     * Create a new job instance.
     *
     * @param $user
     * @return void
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // get Salary data by user id
        // $salary = Salary::find($this->user->id);
        $user = $this->data['user'];
        $salary = $this->data['salary'];

        Log::info('Sending email to ' . $user->email);

        if ($user->notifications != 1) {
            Log::info('Notifications are disabled for the main user. No email sent.');
            return false;
        }

        if ($salary) {
            Mail::to($user->email)->send(new UangMasuk($salary));

            // Check if there is an email_parrent
            if ($user->email_parrent) {
                // Separate email_parrent by comma
                $emailParents = explode(',', $user->email_parrent);

                foreach ($emailParents as $emailParent) {
                    Mail::to($emailParent)->send(new UangMasuk($salary));
                }
            }else{
                Log::info('No email_parrent found for user ' . $user->email);
            }
        }else{
            Log::info('No salary data found for user ' . $user->email);
            return false;
        }
    }
}
