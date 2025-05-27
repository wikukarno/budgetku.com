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
            if (!Schema::hasColumn('finances', 'uuid')) {
                $table->uuid('uuid')->nullable()->after('id')->index();
            }

            if (!Schema::hasColumn('finances', 'users_uuid')) {
                $table->uuid('users_uuid')->nullable()->after('users_id')->index();
            }

            if (!Schema::hasColumn('finances', 'category_finances_uuid')) {
                $table->uuid('category_finances_uuid')->nullable()->after('category_finances_id')->index();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('finances', function (Blueprint $table) {
            if (Schema::hasColumn('finances', 'uuid')) {
                $table->dropColumn('uuid');
            }

            if (Schema::hasColumn('finances', 'users_uuid')) {
                $table->dropColumn('users_uuid');
            }

            if (Schema::hasColumn('finances', 'category_finances_uuid')) {
                $table->dropColumn('category_finances_uuid');
            }
        });
    }
};
