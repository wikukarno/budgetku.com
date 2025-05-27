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
        Schema::table('finances', function (Blueprint $table) {
            // Make users_id nullable
            if (Schema::hasColumn('finances', 'users_id')) {
                $table->string('users_id')->nullable()->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('finances', function (Blueprint $table) {
            // Revert users_id to non-nullable
            if (Schema::hasColumn('finances', 'users_id')) {
                $table->string('users_id')->nullable(false)->change();
            }
        });
    }
};
