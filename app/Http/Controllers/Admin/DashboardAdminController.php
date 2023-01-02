<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CategoryFinance;
use App\Models\Finance;
use App\Models\Portofolio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardAdminController extends Controller
{
    public function index()
    {
        $portofolios = Portofolio::count();
        $finances = Finance::where('users_id', Auth::user()->id)->get();
        $totalPerbulan = $finances->reduce(function ($carry, $item) {
            return $carry + $item->price;
        }, 0);
        $categoryFinances = CategoryFinance::count();
        return view('admin.dashboard', compact('portofolios', 'finances', 'totalPerbulan', 'categoryFinances'));
    }
}
