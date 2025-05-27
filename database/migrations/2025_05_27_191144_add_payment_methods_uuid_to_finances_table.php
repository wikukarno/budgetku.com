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
            if (!Schema::hasColumn('finances', 'payment_methods_uuid')) {
                $table->uuid('payment_methods_uuid')->nullable()->after('purchase_by')->index();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('finances', function (Blueprint $table) {
            if (Schema::hasColumn('finances', 'payment_methods_uuid')) {
                $table->dropColumn('payment_methods_uuid');
            }
        });
    }
};
