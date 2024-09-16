<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryFinanceRequest;
use App\Models\CategoryFinance;
use App\Models\User;
use App\Services\CategoryFinanceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserCategoryFinancesController extends Controller
{

    protected $categoryFinanceService;

    public function __construct(CategoryFinanceService $categoryFinanceService)
    {
        $this->categoryFinanceService = $categoryFinanceService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (request()->ajax()) {
            $query = CategoryFinance::where('users_id', Auth::id());

            return datatables()->of($query)
                ->addIndexColumn()
                ->editColumn('created_at', function ($item) {
                    return $item->created_at->isoFormat('D MMMM Y');
                })
                ->editColumn('updated_at', function ($item) {
                    return $item->updated_at->isoFormat('D MMMM Y');
                })
                ->editColumn('action', function ($item) {
                    return '
                        <a href="javascript:void(0)" class="btn btn-sm btn-secondary" onclick="updateKategoriFinance(' . $item->id . ')">
                            Edit
                        </a>
                        
                        <a href="javascript:void(0)" class="btn btn-sm btn-danger" onclick="deleteKategoriFinance(' . $item->id . ')">
                            Hapus
                        </a>
                    ';
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('user.kategori-finance.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    public function store(CategoryFinanceRequest $request)
    {
        $categoryFinance = CategoryFinance::find($request->id);

        if ($categoryFinance) {
            $this->authorize('updateOrCreate', $categoryFinance);
        }
        $validated = $request->validated();
        $data = $this->categoryFinanceService->updateOrCreateCategoryFinance($validated);
        return response()->json($data);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $data = CategoryFinance::find($request->id);
        if (!$data) {
            // Memberikan response jika data tidak ditemukan
            return response()->json(['status' => 'error', 'message' => 'Data tidak ditemukan'], 404);
        }
        $this->authorize('view', $data, CategoryFinance::class);
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
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $data = CategoryFinance::find($request->id);
        if (!$data) {
            // Memberikan response jika data tidak ditemukan
            return response()->json(['status' => 'error', 'message' => 'Data tidak ditemukan'], 404);
        }

        $this->authorize('delete', $data, CategoryFinance::class);
        $data->delete();
        return response()->json($data);
    }
}
