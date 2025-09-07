<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\AbstractIncomeController;
use App\Services\Admin\SalaryService;

class SalaryController extends AbstractIncomeController
{
    public function __construct(SalaryService $salaryService)
    {
        $this->service = $salaryService;
        parent::__construct();
    }

    protected function getService()
    {
        return $this->service;
    }

    protected function getViewPath(): string
    {
        return 'v2.admin.income';
    }

    protected function getRouteName(): string
    {
        return 'admin.income';
    }
}
