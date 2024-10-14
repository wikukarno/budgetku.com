<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\FinanceRequest;
use App\Jobs\ProcessUangKeluarEmail;
use App\Jobs\ProcessUangMasukEmail;
use App\Mail\UangKeluar;
use App\Models\CategoryFinance;
use App\Models\Finance;
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
            ->where('users_id', Auth::id())
            ->orderBy('created_at', 'DESC');

            return datatables()->of($query)
                ->addIndexColumn()
                ->editColumn('purchase_date', function ($item) {
                    return Carbon::parse($item->purchase_date)->isoFormat('D MMMM Y');
                })
                ->editColumn('price', function ($item) {
                    return 'Rp.' . number_format($item->price, 0, ',', '.');
                })
                ->editColumn('action', function ($item) {
                    return '
                        <a href="' . route('finance.edit', $item->id) . '" class="btn btn-warning">
                            Edit
                        </a>
                        <a href="javascript:void(0)" onclick="deleteFinance(' . $item->id . ')">
                            <button type="button" class="btn btn-danger">Delete</button>
                        </a>
                    ';
                })
                ->rawColumns(['purchase_date', 'action'])
                ->make(true);
        }

        $categories = CategoryFinance::all();
        return view('admin.finance.index', compact('categories'));
    }

    public function create()
    {
        $categories = CategoryFinance::where('users_id', Auth::id())->get();
        return view('admin.finance.create', compact('categories'));
    }


    public function store(Request $request)
    {
        try {

            if($request->hasFile('bukti_pembayaran')) {
                $request->validate([
                    'bukti_pembayaran' => 'image|mimes:jpeg,png,jpg|max:2048',
                ]);

                $bukti_pembayaran = $request->file('bukti_pembayaran')->store('assets/bukti-pembayaran', 'public');

            }else{
                $bukti_pembayaran = null;
            }

            $data = Finance::create([
                'users_id' => Auth::user()->id,
                'category_finances_id' => $request->category_finances_id,
                'name_item' => $request->name_item,
                'price' => str_replace(
                    ['Rp. ', '.'],
                    ['', ''],
                    $request->price
                ),
                'purchase_date' => $request->purchase_date ?? Carbon::now(),
                'purchase_by' => $request->purchase_by ?? 'Tunai',
                'bukti_pembayaran' => $bukti_pembayaran
            ]);

            DB::transaction(function () use ($data) {
                $user = User::findOrFail(Auth::id());
                $user->saldo -= $data->price;
                $user->save();
            });

            $user = User::where('email', 'riskaoktaviana83@gmail.com')->firstOrFail();

            $data = [
                'finance' => $data,
                'user' => $user
            ];

            ProcessUangKeluarEmail::dispatch(
                $data
            );

            if ($data) {
                // return redirect()->route('finance.index')->with('success', 'Data berhasil ditambahkan');
                return to_route('finance.index')->with('success', 'Data berhasil ditambahkan');
            } else {
                // return redirect()->route('finance.index')->with('error', 'Data gagal ditambahkan');
                return to_route('finance.index')->with('error', 'Data gagal ditambahkan');
            }
        } catch (\Throwable $th) {
            // return redirect()->route('finance.index')->with('error', 'Data gagal ditambahkan');
            return to_route('finance.index')->with('error', 'Data gagal ditambahkan');
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
        $categories = CategoryFinance::where('users_id', Auth::id())->get();
        return view('admin.finance.edit', compact('data', 'categories'));
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

            DB::transaction(function () use ($data) {
                $user = User::findOrFail(Auth::id());
                $user->saldo += $data->price;
                $user->save();
            });


            if ($item) {
                // return redirect()->route('finance.index')->with('success', 'Data berhasil diubah');
                return to_route('finance.index')->with('success', 'Data berhasil diubah');
            } else {
                // return redirect()->route('finance.index')->with('error', 'Data gagal diubah');
                return to_route('finance.index')->with('error', 'Data gagal diubah');
            }
        } catch (\Throwable $th) {
            // return redirect()->route('finance.index')->with('error', 'Data gagal diubah');
            return to_route('finance.index')->with('error', 'Data gagal diubah');
        }
    }

    public function destroy(Request $request)
    {
        try {
            $item = Finance::find($request->id);
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
