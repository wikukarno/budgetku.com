<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('uuid_on_users', function (Blueprint $table) {
            // 1. Hilangkan auto_increment dari kolom id
            DB::statement('ALTER TABLE users MODIFY id BIGINT UNSIGNED NOT NULL');

            // 2. Hapus primary key lama dari id
            DB::statement('ALTER TABLE users DROP PRIMARY KEY');

            // 3. Jadikan uuid sebagai primary key
            DB::statement('ALTER TABLE users MODIFY uuid CHAR(36) NOT NULL');
            DB::statement('ALTER TABLE users ADD PRIMARY KEY (`uuid`)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('uuid_on_users', function (Blueprint $table) {
            // Revert back if needed
            DB::statement('ALTER TABLE users DROP PRIMARY KEY');
            DB::statement('ALTER TABLE users MODIFY id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT');
            DB::statement('ALTER TABLE users ADD PRIMARY KEY (`id`)');
            DB::statement('ALTER TABLE users MODIFY uuid CHAR(36) NULL');
        });
    }
};
