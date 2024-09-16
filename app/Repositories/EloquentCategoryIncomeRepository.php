<?php

namespace App\Repositories;

use App\Models\CategoryIncome;

class EloquentCategoryIncomeRepository implements CategoryIncomeRepositoryInterface
{
    public function updateOrCreate(array $attributes, array $values): CategoryIncome
    {
        return CategoryIncome::updateOrCreate($attributes, $values);
    }
}