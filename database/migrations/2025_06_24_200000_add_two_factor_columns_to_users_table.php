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
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('two_factor_enabled')
                ->default(false)
                ->after('password');

            $table->text('two_factor_secret')
                ->after('two_factor_enabled')
                ->nullable();

            $table->text('two_factor_recovery_codes')
                ->after('two_factor_secret')
                ->nullable();

            $table->boolean('two_factor_codes_downloaded')
                ->default(false)
                ->after('two_factor_recovery_codes')
                ->comment('Indicates if the user has downloaded their two-factor authentication codes');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'two_factor_codes_downloaded')) {
                $table->dropColumn('two_factor_codes_downloaded');
            }
            if (Schema::hasColumn('users', 'two_factor_recovery_codes')) {
                $table->dropColumn('two_factor_recovery_codes');
            }
            if (Schema::hasColumn('users', 'two_factor_secret')) {
                $table->dropColumn('two_factor_secret');
            }
            if (Schema::hasColumn('users', 'two_factor_enabled')) {
                $table->dropColumn('two_factor_enabled');
            }
        });
    }
};
