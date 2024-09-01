<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Finance extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'users_id',
        'category_finances_id',
        'name_item',
        'price',
        'purchase_date',
        'purchase_by',
        'bukti_pembayaran',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'users_id', 'id');
    }
    public function category_finance()
    {
        return $this->belongsTo(CategoryFinance::class, 'category_finances_id', 'id');
    }
}
