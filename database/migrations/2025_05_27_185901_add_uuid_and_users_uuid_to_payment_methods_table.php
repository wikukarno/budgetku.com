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
        Schema::table('payment_methods', function (Blueprint $table) {
            if (!Schema::hasColumn('payment_methods', 'uuid')) {
                $table->uuid('uuid')->nullable()->after('id')->index();
            }

            if (!Schema::hasColumn('payment_methods', 'users_uuid')) {
                $table->uuid('users_uuid')->nullable()->after('users_id')->index();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_methods', function (Blueprint $table) {
            if (Schema::hasColumn('payment_methods', 'uuid')) {
                $table->dropColumn('uuid');
            }

            if (Schema::hasColumn('payment_methods', 'users_uuid')) {
                $table->dropColumn('users_uuid');
            }
        });
    }
};
