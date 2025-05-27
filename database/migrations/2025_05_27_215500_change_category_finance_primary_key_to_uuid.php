<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // 1. Hapus AUTO_INCREMENT dari id
        DB::statement('ALTER TABLE category_finances MODIFY id BIGINT UNSIGNED NULL;');

        // 2. Cek apakah primary key masih ada, lalu hapus
        $primary = DB::select("SHOW KEYS FROM category_finances WHERE Key_name = 'PRIMARY'");
        if (!empty($primary)) {
            DB::statement('ALTER TABLE category_finances DROP PRIMARY KEY;');
        }

        // 3. Set uuid sebagai primary key
        Schema::table('category_finances', function (Blueprint $table) {
            $table->primary('uuid');
        });
    }

    public function down(): void
    {
        Schema::table('category_finances', function (Blueprint $table) {
            $table->dropPrimary(); // drop uuid as primary
        });

        DB::statement('ALTER TABLE category_finances MODIFY id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT;');

        Schema::table('category_finances', function (Blueprint $table) {
            $table->primary('id');
        });
    }
};
