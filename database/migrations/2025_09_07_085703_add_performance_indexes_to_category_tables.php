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
        // Add composite indexes for category_finances table
        Schema::table('category_finances', function (Blueprint $table) {
            // Index for common query patterns
            $table->index(['users_uuid', 'created_at'], 'idx_category_finances_user_created');
            $table->index(['users_uuid', 'name_category_finances'], 'idx_category_finances_user_name');
            
            // Index for UUID lookups with user filtering
            $table->index(['uuid', 'users_uuid'], 'idx_category_finances_uuid_user');
        });

        // Add composite indexes for category_incomes table
        Schema::table('category_incomes', function (Blueprint $table) {
            // Index for common query patterns
            $table->index(['users_uuid', 'created_at'], 'idx_category_incomes_user_created');
            $table->index(['users_uuid', 'name_category_incomes'], 'idx_category_incomes_user_name');
            
            // Index for UUID lookups with user filtering
            $table->index(['uuid', 'users_uuid'], 'idx_category_incomes_uuid_user');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop indexes from category_finances table
        Schema::table('category_finances', function (Blueprint $table) {
            $table->dropIndex('idx_category_finances_user_created');
            $table->dropIndex('idx_category_finances_user_name');
            $table->dropIndex('idx_category_finances_uuid_user');
        });

        // Drop indexes from category_incomes table
        Schema::table('category_incomes', function (Blueprint $table) {
            $table->dropIndex('idx_category_incomes_user_created');
            $table->dropIndex('idx_category_incomes_user_name');
            $table->dropIndex('idx_category_incomes_uuid_user');
        });
    }
};
