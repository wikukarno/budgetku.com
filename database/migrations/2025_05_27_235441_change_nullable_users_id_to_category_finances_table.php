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
        Schema::table('category_finances', function (Blueprint $table) {
            // Ubah users_id menjadi nullable
            if (Schema::hasColumn('category_finances', 'users_id')) {
                $table->unsignedBigInteger('users_id')->nullable()->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('category_finances', function (Blueprint $table) {
            // Kembalikan users_id menjadi NOT NULL
            if (Schema::hasColumn('category_finances', 'users_id')) {
                $table->unsignedBigInteger('users_id')->nullable(false)->change();
            }
        });
    }
};
