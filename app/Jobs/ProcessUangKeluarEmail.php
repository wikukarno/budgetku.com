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

        // if finance data exist then send email
        if ($finance) {
            Mail::to($user->email)->send(new UangKeluar($finance));
        } else {
            return false;
        }
    }
}
