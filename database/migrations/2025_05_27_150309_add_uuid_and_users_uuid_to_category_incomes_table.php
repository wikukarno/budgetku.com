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
        Schema::table('category_incomes', function (Blueprint $table) {
            if (!Schema::hasColumn('category_incomes', 'uuid')) {
                $table->uuid('uuid')->after('id')->nullable()->index();
            }

            if (!Schema::hasColumn('category_incomes', 'users_uuid')) {
                $table->uuid('users_uuid')->after('users_id')->nullable()->index();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('category_incomes', function (Blueprint $table) {
            if (Schema::hasColumn('category_incomes', 'uuid')) {
                $table->dropColumn('uuid');
            }

            if (Schema::hasColumn('category_incomes', 'users_uuid')) {
                $table->dropColumn('users_uuid');
            }
        });
    }
};
