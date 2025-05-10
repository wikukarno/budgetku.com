<?php

use App\Models\Finance;
use App\Models\Salary;
use App\Models\User;
use App\Repositories\Admin\BillRepository;
use App\Repositories\Admin\FinanceRepository;
use App\Repositories\Admin\SalaryRepository;
use App\Services\Admin\DashboardService;
use Illuminate\Support\Facades\Config;

it('returns correct dashboard data structure', function () {
    // Arrange
    $user = User::factory()->create();
    Salary::factory()->create([
        'users_id' => $user->id,
        'date' => now()->subDays(10),
        'salary' => 1000000,
    ]);

    Finance::factory()->create([
        'users_id' => $user->id,
        'purchase_date' => now()->subDays(5),
        'price' => 200000,
    ]);

    // Act
    $service = new DashboardService(
        new SalaryRepository(),
        new FinanceRepository(),
        new BillRepository()
    );

    $data = $service->getDashboardData($user);

    // Assert
    expect($data)->toBeArray()
        ->and($data)->toHaveKeys([
            'totalPendapatan',
            'pengeluaran',
            'categoryFinances',
            'todayExpenditure',
            'weeklyReport',
            'anualReport',
            'previeusYearReport',
            'laporanTahunan',
            'monthlyBills',
            'yearlyBills',
            'monthlyReport',
            'keterangan',
            'laporanBulananTahunIni',
            'kategoriBucin',
        ])
        ->and($data['totalPendapatan'])->toEqual(800000);
});

it('renders the admin dashboard with required data', function () {
    Config::set('auth.defaults.guard', 'web');

    $user = User::factory()->create([
        'roles' => 'Owner', // ✅ set sebagai admin
    ]);

    $this->actingAs($user);

    $response = $this->get(route('admin.dashboard'));

    // Kalau masih redirect, log location-nya:
    // dump($response->headers->get('Location'));

    $response
        ->assertOk()
        ->assertViewIs('v2.admin.dashboard')
        ->assertViewHasAll([
            'totalPendapatan',
            'pengeluaran',
            'categoryFinances',
            'todayExpenditure',
            'weeklyReport',
            'anualReport',
            'keterangan',
            'monthlyBills',
            'yearlyBills',
            'monthlyReport',
            'previeusYearReport',
            'laporanTahunan',
            'laporanBulananTahunIni',
            'kategoriBucin',
        ]);
});
