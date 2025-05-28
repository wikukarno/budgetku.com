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
        // ⚠️ Hati-hati: hanya ubah id jadi nullable kalau sudah bukan primary key dan bukan auto_increment
        DB::statement('ALTER TABLE finances MODIFY id BIGINT UNSIGNED NULL;');

        Schema::table('finances', function (Blueprint $table) {
            $table->string('users_id')->nullable()->change();
            $table->string('purchase_by')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('finances', function (Blueprint $table) {
            $table->string('users_id')->nullable(false)->change();
            $table->string('purchase_by')->nullable(false)->change();
        });

        DB::statement('ALTER TABLE finances MODIFY id BIGINT UNSIGNED NOT NULL;');
    }
};
