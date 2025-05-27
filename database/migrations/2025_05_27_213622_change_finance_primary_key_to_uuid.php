<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // 1. Drop auto_increment dari kolom id
        DB::statement('ALTER TABLE finances MODIFY id BIGINT UNSIGNED NULL;');

        // 2. Cek dulu apakah PRIMARY KEY masih ada
        $primaryKeyExists = DB::select("SHOW KEYS FROM finances WHERE Key_name = 'PRIMARY'");
        if (!empty($primaryKeyExists)) {
            DB::statement('ALTER TABLE finances DROP PRIMARY KEY;');
        }

        // 3. Jadikan UUID sebagai PRIMARY KEY
        Schema::table('finances', function (Blueprint $table) {
            $table->primary('uuid');
        });
    }

    public function down(): void
    {
        Schema::table('finances', function (Blueprint $table) {
            $table->dropPrimary();
        });

        DB::statement('ALTER TABLE finances MODIFY id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT;');

        Schema::table('finances', function (Blueprint $table) {
            $table->primary('id');
        });
    }
};
