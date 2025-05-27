<?php

namespace App\Repositories\Admin;

use App\Models\Salary;
use Carbon\Carbon;

class SalaryRepository
{
    public function getDatesLastTwoMonths($userId)
    {
        return Salary::where('users_uuid', $userId)
            ->whereBetween('date', [
                now()->subMonth()->startOfMonth()->toDateString(),
                now()->endOfMonth()->toDateString()
            ])
            ->pluck('date')
            ->toArray();
    }

    public function getTotalSalaryLastTwoMonths($userId)
    {
        return Salary::where('users_uuid', $userId)
            ->whereBetween('date', [
                now()->subMonth()->startOfMonth()->toDateString(),
                now()->endOfMonth()->toDateString()
            ])
            ->sum('salary');
    }

    public function getByUserId($userId)
    {
        return Salary::with('category_income')
            ->where('users_uuid', $userId)
            ->orderByDesc('created_at');
    }

    public function create(array $data)
    {
        return Salary::create($data);
    }

    public function findById($id)
    {
        return Salary::find($id);
    }

    public function findOrFailByUser($id, $userId)
    {
        return Salary::where('users_uuid', $userId)->findOrFail($id);
    }

    public function update(Salary $salary, array $data)
    {
        $salary->update($data);
        return $salary;
    }

    public function delete(Salary $salary)
    {
        return $salary->delete();
    }
}