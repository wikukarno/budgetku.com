<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('user_refresh_tokens', function (Blueprint $table) {
            $table->id();
            $table->string('users_uuid');
            $table->string('token_hash', 64)->index();
            $table->timestamp('expires_at');
            $table->timestamp('revoked_at')->nullable();
            $table->string('user_agent')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_refresh_tokens');
    }
};

