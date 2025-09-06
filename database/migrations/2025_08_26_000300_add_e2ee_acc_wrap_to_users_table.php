<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->longText('e2ee_acc_wrap')->nullable()->after('e2ee_kdf_params');
            $table->string('e2ee_acc_salt', 255)->nullable()->after('e2ee_acc_wrap');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['e2ee_acc_wrap', 'e2ee_acc_salt']);
        });
    }
};

