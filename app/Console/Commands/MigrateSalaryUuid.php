<?php

namespace App\Console\Commands;

use App\Models\Salary;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class MigrateSalaryUuid extends Command
{
    protected $signature = 'migrate:salary-uuid';
    protected $description = 'Force remigrate UUID, users_uuid, dan category_incomes_uuid di tabel salaries';

    public function handle()
    {
        $this->info('🚀 Mulai force-migrasi UUID untuk tabel salaries...');

        Salary::with(['legacyUser', 'legacyCategoryIncome'])
            ->chunkById(100, function ($salaries) {
                foreach ($salaries as $salary) {
                // Force replace UUID
                $salary->uuid = (string) \Ramsey\Uuid\Uuid::uuid4();
                $this->line("✅ New UUID Salary: {$salary->uuid}");

                    // Replace users_uuid
                    if ($salary->legacyUser && $salary->legacyUser->uuid) {
                        $salary->users_uuid = $salary->legacyUser->uuid;
                        $this->line("🧑 User UUID: {$salary->users_uuid}");
                    } else {
                        $salary->users_uuid = null;
                        $this->warn("⚠️ User ID {$salary->users_id} tidak ditemukan atau UUID kosong");
                    }

                    // Replace category_incomes_uuid
                    if ($salary->legacyCategoryIncome && $salary->legacyCategoryIncome->uuid) {
                        $salary->category_incomes_uuid = $salary->legacyCategoryIncome->uuid;
                        $this->line("📂 Category UUID: {$salary->category_incomes_uuid}");
                    } else {
                        $salary->category_incomes_uuid = null;
                        $this->warn("⚠️ Category tipe {$salary->tipe} tidak ditemukan atau UUID kosong");
                    }

                    $salary->save();
                }
            });

        $this->info('✅ Selesai remigrasi UUID dan relasi di tabel salaries!');
    }
}
