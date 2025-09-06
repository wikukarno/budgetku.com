<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserKeypair extends Model
{
    protected $table = 'user_keypairs';

    protected $fillable = [
        'users_uuid', 'version', 'pgp_public_key', 'pgp_private_key_armor', 'active'
    ];
}

