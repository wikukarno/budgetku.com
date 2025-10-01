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
            // Make users_id nullable since we're using users_uuid now
            $table->bigInteger('users_id')->nullable()->unsigned()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_methods', function (Blueprint $table) {
            // Make users_id not nullable again
            $table->bigInteger('users_id')->nullable(false)->unsigned()->change();
        });
    }
};
