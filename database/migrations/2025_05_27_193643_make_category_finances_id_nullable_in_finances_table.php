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
            // Make category_finances_id nullable
            if (Schema::hasColumn('finances', 'category_finances_id')) {
                $table->string('category_finances_id')->nullable()->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('finances', function (Blueprint $table) {
            // Revert category_finances_id to non-nullable
            if (Schema::hasColumn('finances', 'category_finances_id')) {
                $table->string('category_finances_id')->nullable(false)->change();
            }
        });
    }
};
