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

    public function listAll()
    {
        $items = Finance::with(['category_finance', 'legacyCategoryFinance'])
            ->where('users_uuid', Auth::id())
            ->orderBy('created_at', 'DESC')
            ->get()
            ->map(function ($item) {
                $cat = $item->category_finance ?: $item->legacyCategoryFinance;
                $priceFmt = $item->price === '[encrypted]'
                    ? '[encrypted]'
                    : ('Rp. ' . number_format((int)$item->price, 0, ',', '.'));
                return [
                    'uuid' => $item->uuid,
                    'category_finances_uuid' => $item->category_finances_uuid ?: ($cat ? $cat->uuid : null),
                    'category_name' => optional($cat)->name_category_finances,
                    'category_name_pgp' => optional($cat)->name_category_finances_pgp,
                    'name_item' => $item->name_item,
                    'name_item_pgp' => $item->name_item_pgp,
                    'price' => $priceFmt,
                    'price_pgp' => $item->price_pgp,
                    'purchase_date' => optional($item->purchase_date) ? Carbon::parse($item->purchase_date)->format('Y-m-d') : null,
                    'purchase_date_human' => optional($item->purchase_date) ? Carbon::parse($item->purchase_date)->isoFormat('D MMMM Y') : null,
                    'content_key_version' => $item->content_key_version,
                    'action' => '<a href="/pages/admin/expense/edit/' . $item->uuid . '" class="btn btn-sm btn-warning text-white">Edit</a> '
                        . '<a href="javascript:void(0)" class="btn btn-sm btn-danger text-white" onclick="deleteExpense(\'' . $item->uuid . '\')">Delete</a>',
                ];
            });

        return response()->json($items);
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
            'name_item' => 'nullable|string|max:255',
            'name_item_pgp' => 'nullable|string',
            'price' => 'required|string',
            'price_pgp' => 'nullable|string',
            'purchase_date' => 'required|date',
            'payment_methods_uuid' => 'required|exists:payment_methods,uuid',
            // Encrypted files will be uploaded as .pgp text; accept generic file
            'bukti_pembayaran' => 'nullable|file|max:2048',
        ]);

        try {
            // Siapkan payload utama
            $payload = [
                'uuid' => (string) \Illuminate\Support\Str::uuid(),
                'users_uuid' => Auth::id(),
                'category_finances_uuid' => $request->category_finances_uuid,
                'name_item' => $request->name_item,
                'price' => str_replace(['Rp. ', '.'], ['', ''], $request->price),
                'purchase_date' => $request->purchase_date ?? Carbon::now(),
                'payment_methods_uuid' => $request->payment_methods_uuid,
                'bukti_pembayaran' => null,
            ];
            if ($request->filled('name_item_pgp')) {
                $payload['name_item'] = '[encrypted]';
                $payload['name_item_pgp'] = $request->name_item_pgp;
                $payload['content_key_version'] = optional(Auth::user())->key_version ?? 1;
            }
            if ($request->filled('price_pgp')) {
                $payload['price'] = '[encrypted]';
                $payload['price_pgp'] = $request->price_pgp;
                $payload['content_key_version'] = optional(Auth::user())->key_version ?? 1;
            }

            // Simpan data utama tanpa file dulu
            $data = Finance::create($payload);

            // Kalau ada file, baru simpan ke storage dan update data
            if ($request->hasFile('bukti_pembayaran')) {
                // Store encrypted file (could be .pgp). Keep any extension.
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
            'name_item' => 'nullable|string|max:255',
            'name_item_pgp' => 'nullable|string',
            'price' => 'required|string',
            'price_pgp' => 'nullable|string',
            'purchase_date' => 'required|date',
            'payment_methods_uuid' => 'required|exists:payment_methods,uuid',
            'bukti_pembayaran' => 'nullable|file|max:2048', // encrypted file allowed
        ]);

        try {
            
            $data = Finance::findOrFail($id);
            $this->authorize('update', $data);
            // Update kolom utama
            $data->users_uuid = Auth::id();
            $data->category_finances_uuid = $request->category_finances_uuid;
            if ($request->filled('name_item_pgp')) {
                $data->name_item = '[encrypted]';
                $data->name_item_pgp = $request->name_item_pgp;
                $data->content_key_version = optional(Auth::user())->key_version ?? 1;
            } else {
                $data->name_item = $request->name_item;
            }
            if ($request->filled('price_pgp')) {
                $data->price = '[encrypted]';
                $data->price_pgp = $request->price_pgp;
                $data->content_key_version = optional(Auth::user())->key_version ?? 1;
            } else {
                $data->price = str_replace(['Rp. ', '.'], ['', ''], $request->price);
            }
            $data->purchase_date = $request->purchase_date;
            $data->payment_methods_uuid = $request->payment_methods_uuid;
            $updated = $data->save();

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
