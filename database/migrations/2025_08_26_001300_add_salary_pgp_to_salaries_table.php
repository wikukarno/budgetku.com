<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('salaries', function (Blueprint $table) {
            if (!Schema::hasColumn('salaries', 'salary_pgp')) {
                $table->longText('salary_pgp')->nullable()->after('salary');
            }
        });
    }

    public function down(): void
    {
        Schema::table('salaries', function (Blueprint $table) {
            if (Schema::hasColumn('salaries', 'salary_pgp')) {
                $table->dropColumn('salary_pgp');
            }
        });
    }
};

