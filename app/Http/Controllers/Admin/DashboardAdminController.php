<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use App\Models\CategoryFinance;
use App\Models\Finance;
use App\Models\Portofolio;
use App\Models\Salary;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardAdminController extends Controller
{
    public function index()
    {
        $portofolios = Portofolio::count();

        $getMonthly = Salary::where('users_id', Auth::user()->id)
            ->whereMonth('date', '<=', 
            Carbon::now()->startOfMonth()->format('m'))->first();

        $salary = Salary::where('users_id', Auth::user()->id)
            ->where('date', '<=', Carbon::now()->startOfMonth()->format('Y-m-d'))
            ->get();

        $finances = Finance::where('users_id', Auth::user()->id)
        ->whereBetween('purchase_date', [Carbon::now()->startOfMonth()->format('Y-m-d'), Carbon::now()->endOfMonth()->format('Y-m-d')])
        ->get();

        $expenditure = $finances->reduce(function ($carry, $item) {
            return $carry + $item->price;
        }, 0);

        $remainder = $getMonthly->salary - $expenditure;

        $categoryFinances = CategoryFinance::count();

        $todayExpenditure = $finances->where('purchase_date', Carbon::now()->format('Y-m-d'))->reduce(function ($carry, $item) {
            return $carry + $item->price;
        }, 0);

        $weeklyReport = $finances->whereBetween('purchase_date', [Carbon::now()->startOfWeek()->format('Y-m-d'), Carbon::now()->endOfWeek()->format('Y-m-d')])->reduce(function ($carry, $item) {
            return $carry + $item->price;
        }, 0);

        $anualReport = Finance::whereYear('purchase_date', Carbon::now()->format('Y'))->sum('price');

        $keterangan = $expenditure >= $remainder ? 'Bulan ' . Carbon::now()->isoFormat('MMMM') . ' Boros Sekali ' . Auth::user()->name . ''  : 'Anda masih aman dalam pengeluaran';

        $monthlyBills = Bill::where('siklus_tagihan', 0)->sum('harga_tagihan');

        $yearlyBills = Bill::where('siklus_tagihan', 1)->sum('harga_tagihan');


        return view('admin.dashboard', compact(
            'portofolios',
            'getMonthly',
            'salary',
            'finances',
            'expenditure',
            'categoryFinances',
            'remainder',
            'todayExpenditure',
            'weeklyReport',
            'anualReport',
            'keterangan',
            'monthlyBills',
            'yearlyBills'
        ));
    }
}
