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
            if (!Schema::hasColumn('finances', 'purchase_by')) {
                $table->string('purchase_by')->after('purchase_date');
            }

            // Make purchase_by nullable
            $table->string('purchase_by')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('finances', function (Blueprint $table) {
            if (Schema::hasColumn('finances', 'purchase_by')) {
                // Revert purchase_by to non-nullable
                $table->string('purchase_by')->nullable(false)->change();
            }
        });
    }
};
