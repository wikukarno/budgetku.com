<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('user_keypairs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('users_uuid');
            $table->unsignedInteger('version');
            $table->longText('pgp_public_key');
            $table->longText('pgp_private_key_armor');
            $table->boolean('active')->default(false);
            $table->timestamps();

            $table->index(['users_uuid', 'version']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_keypairs');
    }
};

