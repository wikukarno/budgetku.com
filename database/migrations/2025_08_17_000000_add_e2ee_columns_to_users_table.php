<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('e2ee_enabled')->default(false)->after('two_factor_codes_downloaded');
            $table->longText('pgp_public_key')->nullable()->after('e2ee_enabled');
            $table->longText('pgp_private_key_armor')->nullable()->after('pgp_public_key');
            $table->longText('e2ee_pass_wrap')->nullable()->after('pgp_private_key_armor');
            $table->string('e2ee_pass_salt', 255)->nullable()->after('e2ee_pass_wrap');
            $table->longText('e2ee_rec_wrap')->nullable()->after('e2ee_pass_salt');
            $table->string('e2ee_rec_salt', 255)->nullable()->after('e2ee_rec_wrap');
            $table->json('e2ee_kdf_params')->nullable()->after('e2ee_rec_salt');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'e2ee_enabled',
                'pgp_public_key',
                'pgp_private_key_armor',
                'e2ee_pass_wrap',
                'e2ee_pass_salt',
                'e2ee_rec_wrap',
                'e2ee_rec_salt',
                'e2ee_kdf_params',
            ]);
        });
    }
};

