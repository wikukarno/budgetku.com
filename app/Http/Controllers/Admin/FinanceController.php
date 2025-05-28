<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\FinanceRequest;
use App\Jobs\ProcessUangKeluarEmail;
use App\Jobs\ProcessUangMasukEmail;
use App\Mail\UangKeluar;
use App\Models\CategoryFinance;
use App\Models\Finance;
use App\Models\PaymentMethod;
use App\Models\Salary;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;

class FinanceController extends Controller
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
                        <a href="' . route('admin.expense.edit', $item->uuid) . '" class="btn btn-sm btn-warning text-white">
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

        $categories = CategoryFinance::where('users_uuid', Auth::id())->get();
        $filterByYear = Finance::select(DB::raw('YEAR(created_at) as year'))
            ->where('users_uuid', Auth::id())
            ->groupBy('year')
            ->get();
        return view('v2.admin.expense.index', [
            'categories' => $categories,
            'filterByYear' => $filterByYear
        ]);
    }

    public function create()
    {
        $categories = CategoryFinance::where('users_uuid', Auth::id())->get();
        $paymentMethods = PaymentMethod::select('uuid', 'name')->get();
        return view('v2.admin.expense.create', compact('categories', 'paymentMethods'));
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
                'uuid' => (string) \Illuminate\Support\Str::uuid(),
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

            // Kirim email ke user
            $user = Auth::user();

            // Proses saldo & email
            $userId = Auth::id();
            $lastMonth = Carbon::now()->subMonth();

            $pengeluaran = 0;

            $tanggalSemuaGajiBulanKemarinDanBulanIni = Salary::where('users_uuid', $userId)
                ->whereBetween('date', [$lastMonth->startOfMonth()->format('Y-m-d'), Carbon::now()->endOfMonth()->format('Y-m-d')])
                ->pluck('date')->toArray();


            $salary = Salary::where('users_uuid', $userId)
                ->whereBetween('date', [$lastMonth->startOfMonth()->format('Y-m-d'), Carbon::now()->endOfMonth()->format('Y-m-d')])
                ->sum('salary');


            if (!empty($tanggalSemuaGajiBulanKemarinDanBulanIni)) {
                $pengeluaran = Finance::where('users_uuid', $userId)
                    ->whereBetween('purchase_date', [$tanggalSemuaGajiBulanKemarinDanBulanIni[0], Carbon::now()->endOfMonth()->format('Y-m-d')])
                    ->sum('price');
            } else {
                $pengeluaran = 0;
            }

            $sendEmail = [
                'finance' => $data,
                'user' => $user,
                'saldo' => $salary - $pengeluaran
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
        $data = Finance::where('users_uuid', Auth::id())->findOrFail($id);
        $categories = CategoryFinance::where('users_uuid', Auth::id())
            ->get();
        $data->price = 'Rp. ' . number_format($data->price, 0, ',', '.');
        $paymentMethods = PaymentMethod::select('uuid', 'name')->get();
        return view('v2.admin.expense.edit', compact('data', 'categories', 'paymentMethods'));
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

            // Kirim email ke user
            $user = Auth::user();

            // Proses saldo & email
            $userId = Auth::id();
            $lastMonth = Carbon::now()->subMonth();

            $pengeluaran = 0;

            $tanggalSemuaGajiBulanKemarinDanBulanIni = Salary::where('users_uuid', $userId)
                ->whereBetween('date', [$lastMonth->startOfMonth()->format('Y-m-d'), Carbon::now()->endOfMonth()->format('Y-m-d')])
                ->pluck('date')->toArray();


            $salary = Salary::where('users_uuid', $userId)
                ->whereBetween('date', [$lastMonth->startOfMonth()->format('Y-m-d'), Carbon::now()->endOfMonth()->format('Y-m-d')])
                ->sum('salary');


            if (!empty($tanggalSemuaGajiBulanKemarinDanBulanIni)) {
                $pengeluaran = Finance::where('users_uuid', $userId)
                    ->whereBetween('purchase_date', [$tanggalSemuaGajiBulanKemarinDanBulanIni[0], Carbon::now()->endOfMonth()->format('Y-m-d')])
                    ->sum('price');
            } else {
                $pengeluaran = 0;
            }

            $sendEmail = [
                'finance' => $data,
                'user' => $user,
                'saldo' => $salary - $pengeluaran
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

            return response()->json([
                'code' => 200,
                'message' => 'Data deleted successfully'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'code' => 500,
                'message' => 'Data failed to delete'
            ]);
        }
    }
}
