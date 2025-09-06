<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('category_finances', function (Blueprint $table) {
            if (!Schema::hasColumn('category_finances', 'name_category_finances_pgp')) {
                $table->longText('name_category_finances_pgp')->nullable()->after('name_category_finances');
            }
            if (!Schema::hasColumn('category_finances', 'content_key_version')) {
                $table->unsignedInteger('content_key_version')->default(1)->after('name_category_finances_pgp');
            }
        });
    }

    public function down(): void
    {
        Schema::table('category_finances', function (Blueprint $table) {
            if (Schema::hasColumn('category_finances', 'content_key_version')) {
                $table->dropColumn('content_key_version');
            }
            if (Schema::hasColumn('category_finances', 'name_category_finances_pgp')) {
                $table->dropColumn('name_category_finances_pgp');
            }
        });
    }
};

