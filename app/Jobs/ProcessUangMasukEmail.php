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

        // Cek apakah ada email_parrent
        if ($user->email_parrent) {
            // Memisahkan email_parrent berdasarkan koma
            $emailParents = explode(',', $user->email_parrent);

            // Mengirim email ke setiap email orang tua
            foreach ($emailParents as $parentEmail) {
                Mail::to(trim($parentEmail))->send(new UangMasuk($salary));
            }
        }

        // if finance data exist then send email
        // if ($salary) {
        //     Mail::to($user->email)->send(new UangMasuk($salary));
        // }else{
        //     return false;
        // }
    }
}
