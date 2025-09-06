<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Ensure the `id` column is AUTO_INCREMENT primary key
        try {
            DB::statement('ALTER TABLE `payment_methods` MODIFY `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT');
        } catch (\Throwable $e) {
            // ignore if not supported or already correct
        }
    }

    public function down(): void
    {
        // No-op: reverting auto_increment is unnecessary
    }
};

