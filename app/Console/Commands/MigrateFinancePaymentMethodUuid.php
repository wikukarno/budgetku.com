<?php

namespace App\Console\Commands;

use App\Models\Finance;
use App\Models\PaymentMethod;
use Illuminate\Console\Command;

class MigrateFinancePaymentMethodUuid extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:finance-payment-method-uuid';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrasi kolom purchase_by ke payment_methods_uuid di tabel finances';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Memulai migrasi kolom payment_methods_uuid di tabel finances...');

        Finance::whereNull('payment_methods_uuid')
            ->chunk(100, function ($finances) {
                foreach ($finances as $finance) {
                    if ($finance->purchase_by) {
                        $paymentMethod = PaymentMethod::where('id', $finance->purchase_by)->first();

                        if ($paymentMethod && $paymentMethod->uuid) {
                            $finance->payment_methods_uuid = $paymentMethod->uuid;
                            $finance->save();
                        }
                    }
                }
            });

        $this->info('✅ Migrasi selesai!');
    }
}
