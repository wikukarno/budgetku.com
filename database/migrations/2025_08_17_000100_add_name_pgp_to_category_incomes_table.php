<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('category_incomes', function (Blueprint $table) {
            if (!Schema::hasColumn('category_incomes', 'name_category_incomes_pgp')) {
                $table->longText('name_category_incomes_pgp')->nullable()->after('name_category_incomes');
            }
        });
    }

    public function down(): void
    {
        Schema::table('category_incomes', function (Blueprint $table) {
            if (Schema::hasColumn('category_incomes', 'name_category_incomes_pgp')) {
                $table->dropColumn('name_category_incomes_pgp');
            }
        });
    }
};

