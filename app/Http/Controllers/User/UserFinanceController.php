<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\AbstractFinanceController;
use App\Services\User\FinanceService;
use App\Models\Finance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use PDF;

class UserFinanceController extends AbstractFinanceController
{
    protected function getService()
    {
        return app(FinanceService::class);
    }

    protected function getViewPath(): string
    {
        return 'v2.user.expense';
    }

    protected function getRouteName(): string
    {
        return 'customer.expense';
    }

    public function index()
    {
        if (request()->ajax()) {
            return parent::index();
        }

        $categories = Cache::remember("user_categories_finance_" . Auth::id(), 3600, function () {
            return \App\Models\CategoryFinance::where('users_uuid', Auth::id())->get();
        });

        $filterByYear = Cache::remember("finance_years_" . Auth::id(), 3600, function () {
            return Finance::select(DB::raw('YEAR(created_at) as year'))
                ->where('users_uuid', Auth::id())
                ->groupBy('year')
                ->get();
        });

        return view('v2.user.expense.index', [
            'categories' => $categories,
            'filterByYear' => $filterByYear
        ]);
    }

    public function searching(Request $request)
    {
        if (request()->ajax()) {
            $query = $this->service->getSearchQuery($request->year);

            return datatables()->of($query)
                ->addIndexColumn()
                ->editColumn('category_finances_id', fn($item) => $item->category_finance->name_category_finances)
                ->editColumn('purchase_date', fn($item) => Carbon::parse($item->purchase_date)->isoFormat('D MMMM Y'))
                ->editColumn('price', fn($item) => 'Rp.' . number_format($item->price, 0, ',', '.'))
                ->editColumn('action', function ($item) {
                    return '
                        <a href="' . route('customer.expense.edit', $item->uuid) . '" class="btn btn-sm btn-warning text-white">
                            Edit
                        </a>
                        <a href="javascript:void(0)" class="btn btn-sm btn-danger text-white" onclick="deleteExpense(\'' . $item->uuid . '\')">
                            Delete
                        </a>
                    ';
                })
                ->rawColumns(['purchase_date', 'action'])
                ->make(true);
        }

        $categories = \App\Models\CategoryFinance::where('users_uuid', Auth::id())->get();
        $filterByYear = Finance::select(DB::raw('YEAR(created_at) as year'))
            ->where('users_uuid', Auth::id())
            ->groupBy('year')
            ->get();
        return view('v2.user.expense.index', [
            'categories' => $categories,
            'filterByYear' => $filterByYear
        ]);
    }

    public function downloadWeeklyReport($userId)
    {
        $user = User::findOrFail($userId);

        $transactions = $this->service->getWeeklyTransactions(
            Carbon::now()->subWeek()->startOfWeek()->format('Y-m-d'),
            Carbon::now()->subWeek()->endOfWeek()->format('Y-m-d')
        );

        $weeklyTotal = $transactions->sum('price');

        $startDate = $transactions->min('purchase_date') 
            ? Carbon::parse($transactions->min('purchase_date')) 
            : Carbon::now()->subWeek()->startOfWeek();
        
        $endDate = $transactions->max('purchase_date') 
            ? Carbon::parse($transactions->max('purchase_date')) 
            : Carbon::now()->subWeek()->endOfWeek();

        $pdf = Pdf::loadView('pdf.weekly-report', [
            'user' => $user,
            'transactions' => $transactions,
            'weeklyTotal' => $weeklyTotal,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]);

        return $pdf->download('Laporan-Keuangan-Mingguan-' . $user->name . '.pdf');
    }

    public function exportExpense()
    {
        // Implementation for export functionality
        // This would typically generate an Excel or PDF export
        return response()->json([
            'status' => true,
            'message' => 'Export functionality to be implemented'
        ]);
    }
}