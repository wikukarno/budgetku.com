<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('finances', function (Blueprint $table) {
            if (!Schema::hasColumn('finances', 'name_item_pgp')) {
                $table->longText('name_item_pgp')->nullable()->after('name_item');
            }
        });
    }

    public function down(): void
    {
        Schema::table('finances', function (Blueprint $table) {
            if (Schema::hasColumn('finances', 'name_item_pgp')) {
                $table->dropColumn('name_item_pgp');
            }
        });
    }
};

