<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentMethod extends Model
{
    use SoftDeletes;


    protected $fillable = [
        'name',
        'users_id',
        'icon',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'users_id');
    }

    public function finances()
    {
        return $this->hasMany(Finance::class);
    }
}
