<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('salaries', function (Blueprint $table) {
            // Ubah kolom tipe dari enum ke string dan nullable
            $table->string('tipe')->nullable()->change();

            // Ubah juga users_id jadi nullable
            $table->string('users_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('salaries', function (Blueprint $table) {
            // Balik ke enum lagi dan NOT NULL (jika memang perlu)
            $table->enum('tipe', ['gaji', 'bonus', 'thr', 'saham', 'tambahan'])->default('gaji')->change();
            $table->string('users_id')->nullable(false)->change();
        });
    }
};
