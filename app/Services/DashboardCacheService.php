<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use App\Models\Salary;
use App\Models\Finance;

class DashboardCacheService
{
    protected $userId;

    public function __construct()
    {
        $this->userId = Auth::id();
    }

    public function gajiBulanIni()
    {
        return Cache::remember("gaji_bulan_ini_user_{$this->userId}", 3600, function () {
            return Salary::where('users_uuid', $this->userId)
                ->whereMonth('date', Carbon::now()->month)
                ->whereYear('date', Carbon::now()->year)
                ->sum('salary');
        });
    }

    public function gajiBulanLalu()
    {
        $lastMonth = Carbon::now()->subMonth();
        return Cache::remember("gaji_bulan_lalu_user_{$this->userId}", 3600, function () use ($lastMonth) {
            return Salary::where('users_uuid', $this->userId)
                ->whereMonth('date', $lastMonth->month)
                ->whereYear('date', $lastMonth->year)
                ->sum('salary');
        });
    }

    public function pengeluaranBulanIni()
    {
        return Cache::remember("pengeluaran_bulan_ini_user_{$this->userId}", 3600, function () {
            return Finance::where('users_uuid', $this->userId)
                ->whereMonth('purchase_date', Carbon::now()->month)
                ->whereYear('purchase_date', Carbon::now()->year)
                ->sum('price');
        });
    }

    public function pengeluaranBulanLalu()
    {
        $lastMonth = Carbon::now()->subMonth();
        return Cache::remember("pengeluaran_bulan_lalu_user_{$this->userId}", 3600, function () use ($lastMonth) {
            return Finance::where('users_uuid', $this->userId)
                ->whereMonth('purchase_date', $lastMonth->month)
                ->whereYear('purchase_date', $lastMonth->year)
                ->sum('price');
        });
    }

    public function laporanBulananTahunIni()
    {
        return Cache::remember("laporan_tahunan_user_{$this->userId}", 3600, function () {
            return Finance::where('users_uuid', $this->userId)
                ->whereYear('purchase_date', Carbon::now()->year)
                ->selectRaw('MONTH(purchase_date) as month, SUM(price) as total')
                ->groupByRaw('MONTH(purchase_date)')
                ->orderBy('month')
                ->get();
        });
    }

    public function totalSaldo()
    {
        return Cache::remember("total_saldo_user_{$this->userId}", 3600, function () {
            $salary = Salary::where('users_uuid', $this->userId)->sum('salary');
            $spending = Finance::where('users_uuid', $this->userId)->sum('price');
            return $salary - $spending;
        });
    }
}
