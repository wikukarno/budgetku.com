<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessUangKeluarEmail;
use App\Models\CategoryFinance;
use App\Models\Finance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UserFinanceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (request()->ajax()) {
            $query = Finance::with(['category_finance'])
                ->where('users_id', Auth::id())
                ->orderBy('created_at', 'DESC');

            return datatables()->of($query)
                ->addIndexColumn()
                ->editColumn('category_finance', function ($item) {
                    return $item->category_finance->name;
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
        return view('user.expense.index', [
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
        $categories = CategoryFinance::where('users_id', Auth::id())->get();
        return view('user.expense.create', compact('categories'));
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
        $categories = CategoryFinance::where('users_id', Auth::id())
            ->get();
        return view('user.expense.edit', compact('data', 'categories'));
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
                return to_route('expense.index');
            } else {
                return to_route('expense.index');
            }
        } catch (\Throwable $th) {
            return to_route('expense.index');
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
