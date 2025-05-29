<?php

namespace App\Services\Admin;

use App\Models\CategoryFinance;
use App\Repositories\Admin\BillRepository;
use App\Repositories\Admin\FinanceRepository;
use App\Repositories\Admin\SalaryRepository;

class DashboardService
{
    protected $salaryRepo, $financeRepo, $billRepo;

    public function __construct(SalaryRepository $salaryRepo, FinanceRepository $financeRepo, BillRepository $billRepo)
    {
        $this->salaryRepo = $salaryRepo;
        $this->financeRepo = $financeRepo;
        $this->billRepo = $billRepo;
    }

    public function getDashboardData($user)
    {
        $userId = $user->uuid;
        $salaryDates = $this->salaryRepo->getDatesLastTwoMonths($userId);
        $salary = $this->salaryRepo->getTotalSalaryLastTwoMonths($userId);
        $pengeluaran = (!empty($salaryDates))
            ? $this->financeRepo->getPengeluaran($userId, collect($salaryDates)->min())
            : 0;
        $totalPendapatan = $salary - $pengeluaran;


        $monthlyReport = $this->financeRepo->getMonthlyReport($userId);
        $keterangan = $totalPendapatan <= $monthlyReport
            ? 'Bulan ' . now()->isoFormat('MMMM') . ' Boros Sekali ' . $user->name
            : 'Masih aman kok, jangan lupa investasi dan sedekah ya!';

        return [
            'totalPendapatan' => $totalPendapatan,
            'pengeluaran' => $pengeluaran,
            'categoryFinances' => CategoryFinance::count(),
            'todayExpenditure' => $this->financeRepo->getTodayExpenditure($userId),
            'weeklyReport' => $this->financeRepo->getWeeklyReport($userId),
            'anualReport' => $this->financeRepo->getYearlyReport($userId),
            'previeusYearReport' => $this->financeRepo->getLastYearReport($userId),
            'laporanTahunan' => $this->financeRepo->getYearlyReport($userId),
            'monthlyBills' => $this->billRepo->getMonthlyBills(),
            'yearlyBills' => $this->billRepo->getYearlyBills(),
            'monthlyReport' => $monthlyReport,
            'keterangan' => $keterangan,
            'laporanBulananTahunIni' => $this->financeRepo->getMonthlySummaryThisYear($userId),
            'kategoriBucin' => $this->financeRepo->getKategoriBucin()
        ];
    }

    public function getBillDatatable()
    {
        return $this->billRepo->getMonthlyBillQuery();
    }
}