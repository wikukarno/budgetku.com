<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // 1. Ubah kolom id jadi nullable & hapus auto_increment
        DB::statement('ALTER TABLE category_incomes MODIFY id BIGINT UNSIGNED NULL;');

        // 2. Hapus primary key id (kalau masih ada)
        $primary = DB::select("SHOW KEYS FROM category_incomes WHERE Key_name = 'PRIMARY'");
        if (!empty($primary)) {
            DB::statement('ALTER TABLE category_incomes DROP PRIMARY KEY;');
        }

        // 3. Jadikan uuid sebagai primary key
        Schema::table('category_incomes', function (Blueprint $table) {
            $table->primary('uuid');
        });
    }

    public function down(): void
    {
        Schema::table('category_incomes', function (Blueprint $table) {
            $table->dropPrimary(); // drop uuid as primary
        });

        DB::statement('ALTER TABLE category_incomes MODIFY id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT;');

        Schema::table('category_incomes', function (Blueprint $table) {
            $table->primary('id');
        });
    }
};
