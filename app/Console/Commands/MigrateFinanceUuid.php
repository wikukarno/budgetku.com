<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use App\Models\Finance;
use App\Models\CategoryFinance;
use Illuminate\Support\Str;

class MigrateFinanceUuid extends Command
{
    protected $signature = 'migrate:finance-uuid';
    protected $description = 'Migrasi UUID dan foreign key UUID pada tabel finances';

    public function handle()
    {
        $this->info('🚀 Memulai migrasi UUID di tabel finances...');

        Finance::whereNull('uuid')
            ->orWhereNull('users_uuid')
            ->orWhereNull('category_finances_uuid')
            ->chunk(100, function ($finances) {
                foreach ($finances as $finance) {
                    // Set uuid jika belum ada
                    if (empty($finance->uuid)) {
                        $finance->uuid = (string) Str::uuid();
                    }

                    // Mapping users_id -> users_uuid
                    if ($finance->users_id && empty($finance->users_uuid)) {
                        $user = User::find($finance->users_id);
                        if ($user && $user->uuid) {
                            $finance->users_uuid = $user->uuid;
                        }
                    }

                    // Mapping category_finances_id -> category_finances_uuid
                    if ($finance->category_finances_id && empty($finance->category_finances_uuid)) {
                        $cat = CategoryFinance::find($finance->category_finances_id);
                        if ($cat && $cat->uuid) {
                            $finance->category_finances_uuid = $cat->uuid;
                        }
                    }

                    $finance->save();
                }
            });

        $this->info('✅ Migrasi selesai!');
    }
}
