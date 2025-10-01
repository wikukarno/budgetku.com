<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\AbstractFinanceController;
use App\Services\Admin\FinanceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Models\CategoryFinance;
use App\Models\Finance;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class FinanceController extends AbstractFinanceController
{
    protected function getService()
    {
        return app(FinanceService::class);
    }

    protected function getViewPath(): string
    {
        return 'v2.admin.expense';
    }

    protected function getRouteName(): string
    {
        return 'admin.expense';
    }

    public function index()
    {
        if (request()->ajax()) {
            return parent::index();
        }

        $categories = Cache::remember("user_categories_finance_" . Auth::id(), 3600, function () {
            return CategoryFinance::where('users_uuid', Auth::id())->get();
        });

        $filterByYear = Cache::remember("finance_years_" . Auth::id(), 3600, function () {
            return Finance::select(DB::raw('YEAR(created_at) as year'))
                ->where('users_uuid', Auth::id())
                ->groupBy('year')
                ->get();
        });

        return view('v2.admin.expense.index', [
            'categories' => $categories,
            'filterByYear' => $filterByYear
        ]);
    }
}