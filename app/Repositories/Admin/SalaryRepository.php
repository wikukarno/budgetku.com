<?php

namespace App\Repositories\Admin;

use App\Models\Salary;
use Carbon\Carbon;

class SalaryRepository
{
    public function getDatesLastTwoMonths($userId)
    {
        return Salary::where('users_id', $userId)
            ->whereBetween('date', [
                now()->subMonth()->startOfMonth()->toDateString(),
                now()->endOfMonth()->toDateString()
            ])
            ->pluck('date')
            ->toArray();
    }

    public function getTotalSalaryLastTwoMonths($userId)
    {
        return Salary::where('users_id', $userId)
            ->whereBetween('date', [
                now()->subMonth()->startOfMonth()->toDateString(),
                now()->endOfMonth()->toDateString()
            ])
            ->sum('salary');
    }
}