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
            // Drop primary key first
            $table->dropPrimary(['id']);
            
            // Make id nullable
            $table->bigInteger('id')->nullable()->unsigned()->change();
            
            // Set uuid as primary key
            $table->primary('uuid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_methods', function (Blueprint $table) {
            // Drop uuid primary key
            $table->dropPrimary(['uuid']);
            
            // Make id not nullable and primary again
            $table->bigInteger('id')->nullable(false)->unsigned()->change();
            $table->primary('id');
        });
    }
};
