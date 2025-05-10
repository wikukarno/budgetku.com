<?php

namespace App\Repositories\Admin;

use App\Models\Bill;

class BillRepository
{
    public function getMonthlyBills()
    {
        return Bill::where('siklus_tagihan', 0)->sum('harga_tagihan');
    }

    public function getYearlyBills()
    {
        return Bill::where('siklus_tagihan', 1)->sum('harga_tagihan');
    }

    public function getMonthlyBillQuery()
    {
        return Bill::where('siklus_tagihan', 0);
    }
}