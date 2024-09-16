<?php

namespace App\Repositories;

use App\Models\CategoryFinance;

interface CategoryFinanceRepositoryInterface
{
    public function updateOrCreate(array $attributes, array $values): CategoryFinance;
}