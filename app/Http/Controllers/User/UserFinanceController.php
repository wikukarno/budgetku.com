<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessUangKeluarEmail;
use App\Models\CategoryFinance;
use App\Models\Finance;
use App\Models\PaymentMethod;
use App\Models\Salary;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use PDF;

class UserFinanceController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            $query = Finance::with(['category_finance'])
                ->where('users_uuid', Auth::id())
                // ->whereYear('created_at', Carbon::now()->year)
                ->orderBy('created_at', 'DESC');

            return datatables()->of($query)
                ->addIndexColumn()
                ->editColumn('category_finances_uuid', function ($item) {
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

        // $categories = CategoryFinance::where('users_uuid', Auth::id())->get();
        // $filterByYear = Finance::select(DB::raw('YEAR(created_at) as year'))
        //     ->where('users_uuid', Auth::id())
        //     ->groupBy('year')
        //     ->get();

        $categories = Cache::remember("user_categories_finance_" . Auth::id(), 3600, function () {
            return CategoryFinance::where('users_uuid', Auth::id())->get();
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

    public  function searching(Request $request)
    {
        if (request()->ajax()) {
            $query = Finance::with(['category_finance'])
                ->where('users_uuid', Auth::id())
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
                        <a href="' . route('customer.expense.edit', $item->id) . '" class="btn btn-sm btn-warning">
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

        $categories = CategoryFinance::where('users_uuid', Auth::id())->get();
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
        // Cari user berdasarkan ID
        $user = User::findOrFail($userId);

        // Ambil transaksi minggu sebelumnya
        $transactions = Finance::where('users_uuid', $user->uuid)
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
        $categories = Cache::remember("user_categories_finance_" . Auth::id(), 3600, function () {
            return CategoryFinance::where('users_uuid', Auth::id())->get();
        });

        $paymentMethods = Cache::remember("payment_methods", 3600, function () {
            return PaymentMethod::select('uuid', 'name')->get();
        });

        return view('v2.user.expense.create', compact('categories', 'paymentMethods'));
    }

    public function store(Request $request)
    {
        // Validasi dulu sebelum simpan
        $request->validate([
            'category_finances_uuid' => 'required|exists:category_finances,uuid',
            'name_item' => 'required|string|max:255',
            'price' => 'required|string',
            'purchase_date' => 'required|date',
            'payment_methods_uuid' => 'required|exists:payment_methods,uuid',
            'bukti_pembayaran' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        try {
            // Simpan data utama tanpa file dulu
            $data = Finance::create([
                'users_uuid' => Auth::id(),
                'category_finances_uuid' => $request->category_finances_uuid,
                'name_item' => $request->name_item,
                'price' => str_replace(['Rp. ', '.'], ['', ''], $request->price),
                'purchase_date' => $request->purchase_date ?? Carbon::now(),
                'payment_methods_uuid' => $request->payment_methods_uuid,
                'bukti_pembayaran' => null, // sementara null
            ]);

            // Kalau ada file, baru simpan ke storage dan update data
            if ($request->hasFile('bukti_pembayaran')) {
                $file = $request->file('bukti_pembayaran')->store('assets/bukti_pembayaran', 'public');
                $data->update(['bukti_pembayaran' => $file]);
            }

            // Delete cache
            Cache::forget('total_saldo_user_' . Auth::id());
            Cache::forget('gaji_bulan_ini_user_' . Auth::id());
            Cache::forget('gaji_bulan_lalu_user_' . Auth::id());
            Cache::forget('pengeluaran_bulan_ini_user_' . Auth::id());
            Cache::forget('pengeluaran_bulan_lalu_user_' . Auth::id());
            Cache::forget('laporan_tahunan_user_' . Auth::id());

            // Proses saldo & email
            $user = Auth::user();

            $salary = Salary::where('users_uuid', $user->uuid)->sum('salary');
            $pengeluaran = Finance::where('users_uuid', $user->uuid)->sum('price');
            $saldo = $salary - $pengeluaran;

            $sendEmail = [
                'finance' => $data,
                'user' => $user,
                'saldo' => $saldo
            ];

            ProcessUangKeluarEmail::dispatch($sendEmail);

            return response()->json([
                'status' => true,
                'message' => 'Data saved successfully'
            ]);
        } catch (\Throwable $th) {
            Log::error('Finance Store Error: ' . $th->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Data failed to save'
            ]);
        }
    }

    public function show(Request $request)
    {
        $data = Finance::findOrFail($request->uuid);
        return response()->json($data);
    }

    public function edit($id)
    {
        $data = Finance::findOrFail($id);
        $data->price = 'Rp. ' . number_format($data->price, 0, ',', '.');
        
        $categories = Cache::remember("user_categories_finance_" . Auth::id(), 3600, function () {
            return CategoryFinance::where('users_uuid', Auth::id())->get();
        });

        $paymentMethods = Cache::remember("payment_methods", 3600, function () {
            return PaymentMethod::select('uuid', 'name')->get();
        });
        
        return view('v2.user.expense.edit', compact('data', 'categories', 'paymentMethods'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'category_finances_uuid' => 'required|exists:category_finances,uuid',
            'name_item' => 'required|string|max:255',
            'price' => 'required|string',
            'purchase_date' => 'required|date',
            'payment_methods_uuid' => 'required|exists:payment_methods,uuid',
            'bukti_pembayaran' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048', // max 2MB
        ]);

        try {
            $data = Finance::findOrFail($id);
            $this->authorize('update', $data);

            $updated = $data->update([
                'users_uuid' => Auth::id(),
                'category_finances_uuid' => $request->category_finances_uuid,
                'name_item' => $request->name_item,
                'price' => str_replace(['Rp. ', '.'], ['', ''], $request->price),
                'purchase_date' => $request->purchase_date,
                'payment_methods_uuid' => $request->payment_methods_uuid,
            ]);

            if ($updated && $request->hasFile('bukti_pembayaran')) {
                if ($data->bukti_pembayaran && Storage::disk('public')->exists($data->bukti_pembayaran)) {
                    Storage::disk('public')->delete($data->bukti_pembayaran);
                }

                $file = $request->file('bukti_pembayaran')->store('assets/bukti_pembayaran', 'public');

                $data->update(['bukti_pembayaran' => $file]);
            }

            // Delete cache
            Cache::forget('total_saldo_user_' . Auth::id());
            Cache::forget('gaji_bulan_ini_user_' . Auth::id());
            Cache::forget('gaji_bulan_lalu_user_' . Auth::id());
            Cache::forget('pengeluaran_bulan_ini_user_' . Auth::id());
            Cache::forget('pengeluaran_bulan_lalu_user_' . Auth::id());
            Cache::forget('laporan_tahunan_user_' . Auth::id());

            // Hitung ulang saldo user
            $user = Auth::user();
            $salary = Salary::where('users_uuid', $user->uuid)->sum('salary');
            $pengeluaran = Finance::where('users_uuid', $user->uuid)->sum('price');
            $saldo = $salary - $pengeluaran;

            $sendEmail = [
                'finance' => $data,
                'user' => $user,
                'saldo' => $saldo
            ];

            ProcessUangKeluarEmail::dispatch($sendEmail);

            return response()->json([
                'status' => true,
                'message' => 'Data updated successfully'
            ]);
        } catch (\Throwable $th) {
            Log::error('Finance Update Error: ' . $th->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Data failed to update'
            ]);
        }
    }

    public function destroy(Request $request)
    {
        try {
            $item = Finance::findOrFail($request->uuid);
            $this->authorize('delete', $item);

            $item->delete();

            // Delete cache
            Cache::forget('total_saldo_user_' . Auth::id());
            Cache::forget('gaji_bulan_ini_user_' . Auth::id());
            Cache::forget('gaji_bulan_lalu_user_' . Auth::id());
            Cache::forget('pengeluaran_bulan_ini_user_' . Auth::id());
            Cache::forget('pengeluaran_bulan_lalu_user_' . Auth::id());
            Cache::forget('laporan_tahunan_user_' . Auth::id());

            return response()->json([
                'status' => true,
                'message' => 'Data deleted successfully'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Data failed to delete'
            ]);
        }
    }
}
