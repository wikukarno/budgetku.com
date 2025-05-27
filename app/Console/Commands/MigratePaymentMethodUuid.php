<?php

namespace App\Console\Commands;

use App\Models\PaymentMethod;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class MigratePaymentMethodUuid extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:payment-method-uuid';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrasi UUID dan users_uuid di tabel payment_methods';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Mulai migrasi payment_methods...');

        PaymentMethod::with('legacyUser')
            ->whereNull('users_uuid')
            ->chunk(100, function ($methods) {
                foreach ($methods as $method) {
                    if ($method->legacyUser && $method->legacyUser->uuid) {
                        $method->users_uuid = $method->legacyUser->uuid;

                        if (empty($method->uuid)) {
                            $method->uuid = (string) Str::uuid();
                        }

                        $method->save();
                    }
                }
            });

        $this->info('✅ Selesai migrasi!');
    }
}
