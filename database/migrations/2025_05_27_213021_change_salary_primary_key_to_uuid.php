<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // 1. Hapus auto_increment dari kolom id
        DB::statement('ALTER TABLE salaries MODIFY id BIGINT UNSIGNED NULL;');

        // 2. Hapus primary key lama
        Schema::table('salaries', function (Blueprint $table) {
            $table->dropPrimary();
        });

        // 3. Set uuid sebagai primary key baru
        Schema::table('salaries', function (Blueprint $table) {
            $table->primary('uuid');
        });
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
