<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('finances', function (Blueprint $table) {
            if (!Schema::hasColumn('finances', 'price_pgp')) {
                $table->longText('price_pgp')->nullable()->after('price');
            }
            if (!Schema::hasColumn('finances', 'content_key_version')) {
                $table->unsignedInteger('content_key_version')->default(1)->after('price_pgp');
            }
        });
    }

    public function down(): void
    {
        Schema::table('finances', function (Blueprint $table) {
            if (Schema::hasColumn('finances', 'content_key_version')) {
                $table->dropColumn('content_key_version');
            }
            if (Schema::hasColumn('finances', 'price_pgp')) {
                $table->dropColumn('price_pgp');
            }
        });
    }
};

