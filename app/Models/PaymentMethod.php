<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentMethod extends Model
{
    use SoftDeletes;

    // protected $primaryKey = 'uuid';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'uuid',
        'users_uuid',
        'name',
        'users_id',
        'icon',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'users_id');
    }

    public function legacyUser()
    {
        return $this->belongsTo(User::class, 'users_id', 'id');
    }

    protected static function booted()
    {
        static::creating(function ($paymentMethod) {
            if (empty($paymentMethod->uuid)) {
                $paymentMethod->uuid = (string) \Illuminate\Support\Str::uuid();
            }
        });
    }

    public function finances()
    {
        return $this->hasMany(Finance::class);
    }
}
