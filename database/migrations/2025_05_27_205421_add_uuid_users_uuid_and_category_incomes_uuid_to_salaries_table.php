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
            if (!Schema::hasColumn('salaries', 'uuid')) {
                $table->uuid('uuid')->after('id')->index();
            }
            if (!Schema::hasColumn('salaries', 'users_uuid')) {
                $table->uuid('users_uuid')->nullable()->after('users_id')->index();
            }
            if (!Schema::hasColumn('salaries', 'category_incomes_uuid')) {
                $table->uuid('category_incomes_uuid')->nullable()->after('tipe')->index();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('salaries', function (Blueprint $table) {
            if (Schema::hasColumn('salaries', 'uuid')) {
                $table->dropColumn('uuid');
            }
            if (Schema::hasColumn('salaries', 'users_uuid')) {
                $table->dropColumn('users_uuid');
            }
            if (Schema::hasColumn('salaries', 'category_incomes_uuid')) {
                $table->dropColumn('category_incomes_uuid');
            }
        });
    }
};
