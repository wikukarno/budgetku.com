<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('salaries', function (Blueprint $table) {
            if (!Schema::hasColumn('salaries', 'description_pgp')) {
                $table->longText('description_pgp')->nullable()->after('description');
            }
            if (!Schema::hasColumn('salaries', 'content_key_version')) {
                $table->unsignedInteger('content_key_version')->default(1)->after('description_pgp');
            }
        });
    }

    public function down(): void
    {
        Schema::table('salaries', function (Blueprint $table) {
            if (Schema::hasColumn('salaries', 'content_key_version')) {
                $table->dropColumn('content_key_version');
            }
            if (Schema::hasColumn('salaries', 'description_pgp')) {
                $table->dropColumn('description_pgp');
            }
        });
    }
};

