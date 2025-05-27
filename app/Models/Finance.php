<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Finance extends Model
{
    use HasFactory, SoftDeletes;

    // protected $primaryKey = 'uuid';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'uuid',
        'users_uuid',
        'category_finances_uuid',
        'users_id',
        'category_finances_id',
        'name_item',
        'price',
        'purchase_date',
        'payment_methods_uuid',
        'bukti_pembayaran',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'users_uuid', 'uuid');
    }

    public function legacyUser()
    {
        return $this->belongsTo(User::class, 'users_id', 'id');
    }

    public function category_finance()
    {
        return $this->belongsTo(CategoryFinance::class, 'category_finances_uuid', 'uuid');
    }

    public function legacyCategoryFinance()
    {
        return $this->belongsTo(CategoryFinance::class, 'category_finances_id', 'id');
    }

    protected static function booted()
    {
        static::creating(function ($finance) {
            if (empty($finance->uuid)) {
                $finance->uuid = (string) \Illuminate\Support\Str::uuid();
            }
        });
    }

    public function getRouteKeyName()
    {
        return 'uuid';
    }
}
