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
        Schema::table('category_incomes', function (Blueprint $table) {
            DB::statement('ALTER TABLE category_incomes MODIFY id BIGINT UNSIGNED NULL;');
            DB::statement('ALTER TABLE category_incomes MODIFY users_id BIGINT UNSIGNED NULL;');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('category_incomes', function (Blueprint $table) {
            DB::statement('ALTER TABLE category_incomes MODIFY id BIGINT UNSIGNED NOT NULL;');
            DB::statement('ALTER TABLE category_incomes MODIFY users_id BIGINT UNSIGNED NOT NULL;');
        });
    }
};
