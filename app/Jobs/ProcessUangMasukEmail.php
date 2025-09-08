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
        $user = $this->data['user'];
        $salary = $this->data['salary'];

        // Double check notifications are enabled (defense in depth)
        if (!$user->notifications || $user->notifications != 1) {
            Log::info('Email notifications are disabled for user: ' . $user->email . '. Skipping all email notifications.');
            return;
        }

        if (!$salary) {
            Log::warning('No salary data provided for user: ' . $user->email);
            return;
        }

        Log::info('Processing income email notifications for user: ' . $user->email);

        try {
            // Send email to main user
            Mail::to($user->email)->send(new UangMasuk($salary));
            Log::info('Income email sent successfully to main user: ' . $user->email);

            // Send to parent emails if notifications enabled and parent emails exist
            if ($user->email_parrent) {
                $emailParents = array_map('trim', explode(',', $user->email_parrent));
                $emailParents = array_filter($emailParents); // Remove empty values

                foreach ($emailParents as $emailParent) {
                    if (filter_var($emailParent, FILTER_VALIDATE_EMAIL)) {
                        Mail::to($emailParent)->send(new UangMasuk($salary));
                        Log::info('Income email sent successfully to parent: ' . $emailParent);
                    } else {
                        Log::warning('Invalid parent email format: ' . $emailParent . ' for user: ' . $user->email);
                    }
                }
            } else {
                Log::info('No parent email configured for user: ' . $user->email);
            }

        } catch (\Exception $e) {
            Log::error('Failed to send income email for user: ' . $user->email . '. Error: ' . $e->getMessage());
            throw $e; // Re-throw to trigger job retry mechanism
        }
    }
}
