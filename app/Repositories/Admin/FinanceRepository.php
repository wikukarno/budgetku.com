<?php

namespace App\Repositories\Admin;

use App\Models\Finance;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class FinanceRepository
{
    public function getPengeluaran($userId, $startDate)
    {
        return Finance::where('users_uuid', $userId)
            ->whereBetween('purchase_date', [$startDate, now()->endOfMonth()->toDateString()])
            ->sum('price');
    }

    public function getMonthlyReport($userId)
    {
        return Finance::where('users_uuid', $userId)
            ->whereMonth('purchase_date', now()->format('m'))
            ->sum('price');
    }

    public function getTodayExpenditure($userId)
    {
        return Finance::where('users_uuid', $userId)
            ->whereDate('purchase_date', now()->toDateString())
            ->sum('price');
    }

    public function getWeeklyReport($userId)
    {
        return Finance::where('users_uuid', $userId)
            ->whereBetween('purchase_date', [now()->startOfWeek(), now()->endOfWeek()])
            ->sum('price');
    }

    public function getLastYearReport($userId)
    {
        return Finance::where('users_uuid', $userId)
            ->whereYear('purchase_date', now()->subYear()->year)
            ->sum('price');
    }

    public function getYearlyReport($userId)
    {
        return Finance::where('users_uuid', $userId)
            ->whereYear('purchase_date', now()->year)
            ->sum('price');
    }

    public function getMonthlySummaryThisYear($userId)
    {
        return Finance::where('users_uuid', $userId)
            ->whereYear('purchase_date', now()->year)
            ->select(DB::raw('MONTH(purchase_date) as month'), DB::raw('SUM(price) as total'))
            ->groupBy(DB::raw('MONTH(purchase_date)'))
            ->orderBy('month')
            ->get();
    }

    public function getKategoriBucin()
    {
        return Finance::where('category_finances_id', 31)->sum('price');
    }
}