<?php

namespace App\Repositories;

use App\Models\CategoryIncome;

interface CategoryIncomeRepositoryInterface
{
    public function updateOrCreate(array $attributes, array $values): CategoryIncome;
}