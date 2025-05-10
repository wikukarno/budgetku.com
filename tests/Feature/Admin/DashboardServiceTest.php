<?php

use App\Models\User;
use App\Models\Salary;
use App\Models\Finance;
use App\Services\Admin\DashboardService;
use App\Repositories\Admin\SalaryRepository;
use App\Repositories\Admin\FinanceRepository;
use App\Repositories\Admin\BillRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);


