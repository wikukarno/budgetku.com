<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessUangKeluarEmail;
use App\Models\CategoryFinance;
use App\Models\Finance;
use App\Models\Salary;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Exceptions\Exception;
use PDF;

class UserFinanceController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            $query = Finance::with(['category_finance'])
                ->where('users_id', Auth::id())
                ->whereYear('created_at', Carbon::now()->year)
                ->orderBy('created_at', 'DESC');

            return datatables()->of($query)
                ->addIndexColumn()
                ->editColumn('category_finances_id', function ($item) {
                    return $item->category_finance->name_category_finances;
                })
                ->editColumn('purchase_date', function ($item) {
                    return Carbon::parse($item->purchase_date)->isoFormat('D MMMM Y');
                })
                ->editColumn('price', function ($item) {
                    return 'Rp.' . number_format($item->price, 0, ',', '.');
                })
                ->editColumn('action', function ($item) {
                    return '
                        <a href="' . route('expense.edit', $item->id) . '" class="btn btn-sm btn-warning">
                            Edit
                        </a>
                        <a href="javascript:void(0)" class="btn btn-sm btn-danger" onclick="deleteExpense(' . $item->id . ')">
                            Delete
                        </a>
                    ';
                })
                ->rawColumns(['purchase_date', 'action'])
                ->make(true);
        }

        $categories = CategoryFinance::where('users_id', Auth::id())->get();
        $filterByYear = Finance::select(DB::raw('YEAR(created_at) as year'))
            ->where('users_id', Auth::id())
            ->groupBy('year')
            ->get();
        return view('user.expense.index', [
            'categories' => $categories,
            'filterByYear' => $filterByYear
        ]);
    }

    public  function searching(Request $request)
    {
        if (request()->ajax()) {
            $query = Finance::with(['category_finance'])
                ->where('users_id', Auth::id())
                ->whereYear('created_at', $request->year)
                ->orderBy('created_at', 'DESC');

            return datatables()->of($query)
                ->addIndexColumn()
                ->editColumn('category_finances_id', function ($item) {
                    return $item->category_finance->name_category_finances;
                })
                ->editColumn('purchase_date', function ($item) {
                    return Carbon::parse($item->purchase_date)->isoFormat('D MMMM Y');
                })
                ->editColumn('price', function ($item) {
                    return 'Rp.' . number_format($item->price, 0, ',', '.');
                })
                ->editColumn('action', function ($item) {
                    return '
                        <a href="' . route('expense.edit', $item->id) . '" class="btn btn-sm btn-warning">
                            Edit
                        </a>
                        <a href="javascript:void(0)" class="btn btn-sm btn-danger" onclick="deleteExpense(' . $item->id . ')">
                            Delete
                        </a>
                    ';
                })
                ->rawColumns(['purchase_date', 'action'])
                ->make(true);
        }

        $categories = CategoryFinance::where('users_id', Auth::id())->get();
        $filterByYear = Finance::select(DB::raw('YEAR(created_at) as year'))
            ->where('users_id', Auth::id())
            ->groupBy('year')
            ->get();
        return view('user.expense.index', [
            'categories' => $categories,
            'filterByYear' => $filterByYear
        ]);
    }

    public function downloadWeeklyReport($userId)
    {
        // Cari user berdasarkan ID
        $user = User::findOrFail($userId);

        // Ambil transaksi minggu sebelumnya
        $transactions = Finance::where('users_id', $user->id)
            ->whereBetween('purchase_date', [Carbon::now()->subWeek()->startOfWeek(), Carbon::now()->subWeek()->endOfWeek()])
            ->get();

        // Hitung total transaksi
        $weeklyTotal = $transactions->sum('price');

        // Ambil tanggal transaksi pertama dan terakhir
        $startDate = $transactions->min('purchase_date') ? Carbon::parse($transactions->min('purchase_date')) : Carbon::now()->subWeek()->startOfWeek();
        $endDate = $transactions->max('purchase_date') ? Carbon::parse($transactions->max('purchase_date')) : Carbon::now()->subWeek()->endOfWeek();

        // Generate PDF
        $pdf = Pdf::loadView('pdf.weekly-report', [
            'user' => $user,
            'transactions' => $transactions,
            'weeklyTotal' => $weeklyTotal,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]);

        // Return file PDF sebagai download
        return $pdf->download('Laporan-Keuangan-Mingguan-' . $user->name . '.pdf');
    }
    public function create()
    {
        $categories = CategoryFinance::where('users_id', Auth::id())->get();
        return view('user.expense.create', compact('categories'));
    }

    public function store(Request $request)
    {
        try {

            if($request->hasFile('bukti_pembayaran')) {
                $file = $request->file('bukti_pembayaran')->store('assets/bukti_pembayaran', 'public');
            }else{
                $file = null;
            }

            $data = Finance::create([
                'users_id' => Auth::id(),
                'category_finances_id' => $request->category_finances_id,
                'name_item' => $request->name_item,
                'price' => str_replace(
                    ['Rp. ', '.'],
                    ['', ''],
                    $request->price
                ),
                'purchase_date' => $request->purchase_date ?? Carbon::now(),
                'purchase_by' => $request->purchase_by ?? 'Tunai',
                'bukti_pembayaran' => $file
            ]);

            $user = User::where('email', Auth::user()->email)->first();

            $userId = Auth::user()->id;

            $salary = Salary::where('users_id', $userId)
            ->sum('salary');

            $pengeluaran = Finance::where('users_id', $userId)
                ->sum('price');

            $saldo = $salary - $pengeluaran;

            $sendEmail = [
                'finance' => $data,
                'user' => $user,
                'saldo' => $saldo
            ];

            ProcessUangKeluarEmail::dispatch($sendEmail);

            if ($data) {
                return to_route('expense.index');
            } else {
                return to_route('expense.index');
            }
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            return to_route('expense.index');
        }
    }

    public function show(Request $request)
    {
        $data = Finance::findOrFail($request->id);
        return response()->json($data);
    }

    public function edit($id)
    {
        $data = Finance::findOrFail($id);
        $categories = CategoryFinance::where('users_id', Auth::id())
            ->get();
        return view('user.expense.edit', compact('data', 'categories'));
    }

    public function update(Request $request, $id)
    {
        if($request->hasFile('bukti_pembayaran')) {
            $file = $request->file('bukti_pembayaran')->store('assets/bukti_pembayaran', 'public');
        }

        try {
            $data = Finance::findOrFail($id);
            $this->authorize('update', $data);
            $item = $data->update([
                'users_id' => Auth::user()->id,
                'category_finances_id' => $request->category_finances_id,
                'name_item' => $request->name_item,
                'price' => str_replace(
                    ['Rp. ', '.'],
                    ['', ''],
                    $request->price
                ),
                'purchase_date' => $request->purchase_date,
                'purchase_by' => $request->purchase_by,
                'bukti_pembayaran' => $file ?? $data->bukti_pembayaran
            ]);

            $user = User::where('email', Auth::user()->email)->first();

            $userId = Auth::user()->id;

            $salary = Salary::where('users_id', $userId)
            ->sum('salary');

            $pengeluaran = Finance::where('users_id', $userId)
            ->sum('price');

            $saldo = $salary - $pengeluaran;

            $sendEmail = [
                'finance' => $item,
                'user' => $user,
                'saldo' => $saldo
            ];

            ProcessUangKeluarEmail::dispatch($sendEmail);


            if ($item) {
                return to_route('expense.index');
            } else {
                return to_route('expense.index');
            }
        } catch (\Throwable $th) {
            return to_route('expense.index');
        }
    }

    public function destroy(Request $request)
    {
        try {
            $item = Finance::findOrFail($request->id);
            $this->authorize('delete', $item);

            $item->delete();

            return response()->json([
                'code' => 200,
                'message' => 'Data berhasil dihapus'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'code' => 500,
                'message' => 'Data gagal dihapus'
            ]);
        }
    }
}
