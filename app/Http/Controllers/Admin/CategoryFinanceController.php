<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryFinanceRequest;
use App\Models\CategoryFinance;
use App\Services\CategoryFinanceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CategoryFinanceController extends Controller
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
            $query = CategoryFinance::where('users_uuid', Auth::id())->orderBy('created_at', 'DESC');

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
                        <a href="javascript:void(0)" onclick="updateKategoriFinance(' . $item->id . ')">
                            <button type="button" class="btn btn-warning text-white">Edit</button>
                        </a>
                        
                        <a href="javascript:void(0)" onclick="deleteKategoriFinance(' . $item->id . ')">
                            <button type="button" class="btn btn-danger text-white">Delete</button>
                        </a>
                    ';
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('v2.admin.category.expense.index');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
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
        $data = CategoryFinance::where('id', $request->id)
            ->where('users_id', Auth::id())
            ->firstOrFail();

        $this->authorize('view', $data);

        return response()->json($data);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $data = CategoryFinance::where('id', $request->id)
            ->where('users_id', Auth::id())
            ->firstOrFail();

        $this->authorize('delete', $data);
        $data->delete();
        return response()->json(
            [
                'status' => true,
                'message' => 'Data deleted successfully'
            ]
        );
    }
}
