<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CategoryIncome extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'users_id',
        'name_category_incomes',
    ];

    protected $casts = [
        'id' => 'integer',
        'users_id' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'users_id', 'id');
    }

}
