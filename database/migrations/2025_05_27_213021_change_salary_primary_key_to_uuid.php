<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Drop auto_increment dari id
        DB::statement('ALTER TABLE salaries MODIFY id BIGINT UNSIGNED NULL;');

        // Drop primary key HANYA kalau masih ada
        try {
            DB::statement('ALTER TABLE salaries DROP PRIMARY KEY;');
        } catch (\Throwable $e) {
            echo "⚠️ PRIMARY KEY sudah tidak ada. Lanjut...\n";
        }

        // Jadikan uuid sebagai PRIMARY KEY (jika belum)
        try {
            DB::statement('ALTER TABLE salaries ADD PRIMARY KEY (uuid);');
        } catch (\Throwable $e) {
            echo "⚠️ UUID sudah jadi primary key. Lanjut...\n";
        }
    }


    public function down(): void
    {
        // Rollback: kembalikan id sebagai primary key dan auto_increment
        Schema::table('salaries', function (Blueprint $table) {
            $table->dropPrimary(); // drop uuid as primary
        });

        DB::statement('ALTER TABLE salaries MODIFY id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT;');

        Schema::table('salaries', function (Blueprint $table) {
            $table->primary('id');
        });
    }
};
