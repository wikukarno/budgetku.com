<?php

namespace App\Repositories;

use App\Models\CategoryFinance;

class EloquentCategoryFinanceRepository implements CategoryFinanceRepositoryInterface
{
    public function updateOrCreate(array $attributes, array $values): CategoryFinance
    {
        return CategoryFinance::updateOrCreate($attributes, $values);
    }
}