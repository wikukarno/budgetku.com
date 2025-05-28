<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Salary extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'uuid';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'uuid',
        'users_uuid',
        'category_incomes_uuid',
        'salary',
        'date',
        'description',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'users_uuid', 'uuid');
    }

    public function legacyUser()
    {
        return $this->belongsTo(User::class, 'users_id', 'id');
    }

    public function category_income()
    {
        return $this->belongsTo(CategoryIncome::class, 'category_incomes_uuid', 'uuid');
    }

    public function legacyCategoryIncome()
    {
        return $this->belongsTo(CategoryIncome::class, 'tipe', 'id');
    }

    protected static function booted()
    {
        static::creating(function ($salary) {
            if (empty($salary->uuid)) {
                $salary->uuid = (string) \Illuminate\Support\Str::uuid();
            }
        });
    }
}
