<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\AbstractIncomeController;

class SalaryController extends AbstractIncomeController
{
    protected function getService()
    {
        return null;
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
