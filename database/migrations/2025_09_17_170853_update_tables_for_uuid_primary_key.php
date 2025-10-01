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
        // Update users table to use UUID as primary key
        Schema::table('users', function (Blueprint $table) {
            $table->dropPrimary(['id']);
            $table->char('uuid', 36)->primary()->after('id');
            $table->bigInteger('id')->nullable()->change();
            $table->boolean('two_factor_enabled')->default(false)->after('password');
            $table->text('two_factor_secret')->nullable()->after('two_factor_enabled');
            $table->text('two_factor_recovery_codes')->nullable()->after('two_factor_secret');
            $table->timestamp('two_factor_confirmed_at')->nullable()->after('two_factor_recovery_codes');
            $table->boolean('two_factor_codes_downloaded')->default(false)->after('two_factor_confirmed_at');
        });

        // Update finances table
        Schema::table('finances', function (Blueprint $table) {
            $table->dropPrimary(['id']);
            $table->char('uuid', 36)->primary()->after('id');
            $table->bigInteger('id')->nullable()->change();
            $table->string('users_id')->nullable()->after('uuid');
            $table->char('users_uuid', 36)->nullable()->after('users_id');
            $table->string('category_finances_id')->nullable()->after('users_uuid');
            $table->char('category_finances_uuid', 36)->nullable()->after('category_finances_id');
            $table->char('payment_methods_uuid', 36)->nullable()->after('purchase_by');
        });

        // Update salaries table
        Schema::table('salaries', function (Blueprint $table) {
            $table->dropPrimary(['id']);
            $table->char('uuid', 36)->primary()->after('id');
            $table->bigInteger('id')->nullable()->change();
            $table->string('users_id')->nullable()->after('uuid');
            $table->char('users_uuid', 36)->nullable()->after('users_id');
            $table->char('category_incomes_uuid', 36)->nullable()->after('users_uuid');
        });

        // Update category_finances table
        Schema::table('category_finances', function (Blueprint $table) {
            $table->dropPrimary(['id']);
            $table->char('uuid', 36)->primary()->after('id');
            $table->bigInteger('id')->nullable()->change();
            $table->string('users_id')->nullable()->after('uuid');
            $table->char('users_uuid', 36)->nullable()->after('users_id');
        });

        // Update category_incomes table
        Schema::table('category_incomes', function (Blueprint $table) {
            $table->dropPrimary(['id']);
            $table->char('uuid', 36)->primary()->after('id');
            $table->bigInteger('id')->nullable()->change();
            $table->string('users_id')->nullable()->after('uuid');
            $table->char('users_uuid', 36)->nullable()->after('users_id');
        });

        // Update payment_methods table
        Schema::table('payment_methods', function (Blueprint $table) {
            $table->dropPrimary(['id']);
            $table->char('uuid', 36)->primary()->after('id');
            $table->bigInteger('id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert all changes
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['uuid', 'two_factor_enabled', 'two_factor_secret', 'two_factor_recovery_codes', 'two_factor_confirmed_at', 'two_factor_codes_downloaded']);
            $table->bigInteger('id')->autoIncrement()->change();
            $table->primary('id');
        });

        Schema::table('finances', function (Blueprint $table) {
            $table->dropColumn(['uuid', 'users_id', 'users_uuid', 'category_finances_id', 'category_finances_uuid', 'payment_methods_uuid']);
            $table->bigInteger('id')->autoIncrement()->change();
            $table->primary('id');
        });

        Schema::table('salaries', function (Blueprint $table) {
            $table->dropColumn(['uuid', 'users_id', 'users_uuid', 'category_incomes_uuid']);
            $table->bigInteger('id')->autoIncrement()->change();
            $table->primary('id');
        });

        Schema::table('category_finances', function (Blueprint $table) {
            $table->dropColumn(['uuid', 'users_id', 'users_uuid']);
            $table->bigInteger('id')->autoIncrement()->change();
            $table->primary('id');
        });

        Schema::table('category_incomes', function (Blueprint $table) {
            $table->dropColumn(['uuid', 'users_id', 'users_uuid']);
            $table->bigInteger('id')->autoIncrement()->change();
            $table->primary('id');
        });

        Schema::table('payment_methods', function (Blueprint $table) {
            $table->dropColumn(['uuid']);
            $table->bigInteger('id')->autoIncrement()->change();
            $table->primary('id');
        });
    }
};
