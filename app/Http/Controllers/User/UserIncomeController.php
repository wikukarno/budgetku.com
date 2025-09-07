<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\AbstractIncomeController;

class UserIncomeController extends AbstractIncomeController
{
    protected function getService()
    {
        return null;
    }

    protected function getViewPath(): string
    {
        return 'v2.user.income';
    }

    protected function getRouteName(): string
    {
        return 'customer.income';
    }
}
