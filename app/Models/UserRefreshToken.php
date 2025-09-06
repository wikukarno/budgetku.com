<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserRefreshToken extends Model
{
    protected $table = 'user_refresh_tokens';
    protected $fillable = [
        'users_uuid', 'token_hash', 'expires_at', 'revoked_at', 'user_agent', 'ip_address'
    ];
    protected $casts = [
        'expires_at' => 'datetime',
        'revoked_at' => 'datetime',
    ];
}

