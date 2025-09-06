<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('payment_methods', function (Blueprint $table) {
            if (!Schema::hasColumn('payment_methods', 'name_pgp')) {
                $table->longText('name_pgp')->nullable()->after('name');
            }
            if (!Schema::hasColumn('payment_methods', 'content_key_version')) {
                $table->unsignedInteger('content_key_version')->default(1)->after('name_pgp');
            }
            if (!Schema::hasColumn('payment_methods', 'users_uuid')) {
                $table->uuid('users_uuid')->nullable()->after('uuid')->index();
            }
        });
    }

    public function down(): void
    {
        Schema::table('payment_methods', function (Blueprint $table) {
            if (Schema::hasColumn('payment_methods', 'content_key_version')) {
                $table->dropColumn('content_key_version');
            }
            if (Schema::hasColumn('payment_methods', 'name_pgp')) {
                $table->dropColumn('name_pgp');
            }
            // Do not drop users_uuid in down to avoid data loss if other code depends on it
        });
    }
};

