<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payment_methods', function (Blueprint $table) {
            // Make users_id nullable to support UUID-primary users where legacy id can be null
            $table->unsignedBigInteger('users_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('payment_methods', function (Blueprint $table) {
            $table->unsignedBigInteger('users_id')->nullable(false)->change();
        });
    }
};

