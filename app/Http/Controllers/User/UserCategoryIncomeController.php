<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Models\CategoryFinance;
use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryIncomeRequest;
use App\Services\CategoryIncomeService;
use App\Models\CategoryIncome;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UserCategoryIncomeController extends Controller
{

    protected $categoryIncomeService;

    public function __construct(CategoryIncomeService $categoryIncomeService)
    {
        $this->categoryIncomeService = $categoryIncomeService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (request()->ajax()) {
            $query = CategoryIncome::where('users_id', Auth::id());

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
                        <a href="javascript:void(0)" class="btn btn-sm btn-warning" onclick="updateKategoriIncome(' . $item->id . ')">
                            Edit
                        </a>
                        
                        <a href="javascript:void(0)" class="btn btn-sm btn-danger" onclick="deleteKategoriIncome(' . $item->id . ')">
                            Delete
                        </a>
                    ';
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('user.kategori-income.index');
    }

    public function indexv2()
    {
        if (request()->ajax()) {
            $query = CategoryIncome::where('users_id', Auth::id());

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
                        <a href="javascript:void(0)" class="btn btn-sm btn-warning text-white" onclick="updateKategoriIncome(' . $item->id . ')">
                            Edit
                        </a>
                        
                        <a href="javascript:void(0)" class="btn btn-sm btn-danger text-white" onclick="deleteKategoriIncome(' . $item->id . ')">
                            Delete
                        </a>
                    ';
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('v2.user.category.income.index');
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

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CategoryIncomeRequest $request)
    {
        $categoryIncome = CategoryIncome::find($request->id);

        if($categoryIncome){
            $this->authorize('updateOrCreate', $categoryIncome);
        }

        $validated = $request->validated();
        $data = $this->categoryIncomeService->updateOrCreateCategoryIncome($validated);
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
        $data = CategoryIncome::findOrFail($request->id);
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
        $data = CategoryIncome::find($request->id);
        $this->authorize('delete', $data);
        $data->delete();
        return response()->json($data);
    }
}
