<?php

namespace App\Console\Commands;

use App\Models\CategoryFinance;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class MigrateCategoryFinanceUuid extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:category-finance-uuid';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Mulai migrasi category_finances...');

        CategoryFinance::with('legacyUser')
            ->whereNull('users_uuid')
            ->chunk(100, function ($items) {
                foreach ($items as $item) {
                    if ($item->legacyUser && $item->legacyUser->uuid) {
                        $item->users_uuid = $item->legacyUser->uuid;

                        if (empty($item->uuid)) {
                            $item->uuid = (string) Str::uuid();
                        }

                        $item->save();
                    }
                }
            });

        $this->info('✅ Selesai migrasi!');
    }
}
