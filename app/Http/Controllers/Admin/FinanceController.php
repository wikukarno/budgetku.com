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
use Illuminate\Support\Facades\Mail;
use Symfony\Component\Process\Process;

class FinanceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (request()->ajax()) {
            $query = Finance::with(['category_finance'])->orderBy('created_at', 'DESC');

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
        return view('admin.finance.index', [
            'categories' => $categories
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = CategoryFinance::all();
        return view('admin.finance.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
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
            ]);

            // $user = User::where('email', 'riskaoktaviana83@gmail.com')->firstOrFail();
            $user = User::where('email', 'prasetyagama2@gmail.com')->firstOrFail();
            
            $data = [
                'finance' => $data,
                'user' => $user
            ];

            ProcessUangKeluarEmail::dispatch(
                $data
            );

            if ($data) {
                return redirect()->route('finance.index')->with('success', 'Data berhasil ditambahkan');
            } else {
                return redirect()->route('finance.index')->with('error', 'Data gagal ditambahkan');
            }
        } catch (\Throwable $th) {
            return redirect()->route('finance.index')->with('error', 'Data gagal ditambahkan');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $data = Finance::findOrFail($request->id);
        return response()->json($data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = Finance::findOrFail($id);
        $categories = CategoryFinance::all();
        return view('admin.finance.edit', compact('data', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
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
            ]);


            if ($item) {
                return redirect()->route('finance.index')->with('success', 'Data berhasil diubah');
            } else {
                return redirect()->route('finance.index')->with('error', 'Data gagal diubah');
            }
        } catch (\Throwable $th) {
            return redirect()->route('finance.index')->with('error', 'Data gagal diubah');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
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
