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
        // Hapus primary key lama (dari kolom 'id')
        DB::statement('ALTER TABLE users DROP PRIMARY KEY');

        // Ubah kolom uuid jadi NOT NULL (harus sudah terisi sebelumnya)
        DB::statement('ALTER TABLE users MODIFY uuid CHAR(36) NOT NULL');

        // Jadikan uuid sebagai primary key
        DB::statement('ALTER TABLE users ADD PRIMARY KEY (`uuid`)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE users DROP PRIMARY KEY');
        DB::statement('ALTER TABLE users MODIFY uuid CHAR(36) NULL');
        DB::statement('ALTER TABLE users ADD PRIMARY KEY (`id`)');
    }
};
