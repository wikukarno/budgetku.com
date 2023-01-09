<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CategoryFinance;
use App\Models\Finance;
use App\Models\Portofolio;
use App\Models\Salary;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardAdminController extends Controller
{
    public function index()
    {
        $portofolios = Portofolio::count();
        $salary = Salary::where('users_id', Auth::user()->id)->get();
        $finances = Finance::where('users_id', Auth::user()->id)->get();
        $expenditure = $finances->reduce(function ($carry, $item) {
            return $carry + $item->price;
        }, 0);

        $remainder = $salary->reduce(function ($carry, $item) {
            return $carry + $item->salary;
        }, 0) - $expenditure;
        $categoryFinances = CategoryFinance::count();
        // make todayExpenditure create date now

        $todayExpenditure = $finances->where('created_at', Carbon::now())->reduce(function ($carry, $item) {
            return $carry + $item->price;
        }, 0);

        // $todayExpenditure = $finances->where('created_at')->reduce(function ($carry, $item) {
        //     return $carry + $item->price;
        // }, 0);
        return view('admin.dashboard', compact('portofolios', 'finances', 'expenditure', 'categoryFinances', 'remainder', 'todayExpenditure'));
    }
}
