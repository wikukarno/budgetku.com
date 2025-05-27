<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // 1. Hapus AUTO_INCREMENT dulu
        DB::statement('ALTER TABLE salaries MODIFY id BIGINT UNSIGNED NULL;');

        // 2. Drop primary key pakai raw SQL
        DB::statement('ALTER TABLE salaries DROP PRIMARY KEY;');

        // 3. Jadikan uuid sebagai primary key
        DB::statement('ALTER TABLE salaries ADD PRIMARY KEY (uuid);');
    }


    public function down(): void
    {
        DB::statement('ALTER TABLE salaries DROP PRIMARY KEY;');
        DB::statement('ALTER TABLE salaries MODIFY id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT;');
        DB::statement('ALTER TABLE salaries ADD PRIMARY KEY (id);');
    }
};
