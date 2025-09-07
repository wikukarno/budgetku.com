<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryIncomeRequest;
use App\Models\CategoryIncome;
use App\Services\CategoryIncomeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class CategoryIncomeController extends Controller
{
    protected $categoryIncomeService;

    public function __construct(CategoryIncomeService $categoryIncomeService)
    {
        $this->categoryIncomeService = $categoryIncomeService;
    }
    public function index()
    {
        if (request()->ajax()) {
            $query = CategoryIncome::where('users_uuid', Auth::id())->orderBy('created_at', 'DESC');

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
                        <a href="javascript:void(0)" class="btn btn-sm btn-warning text-white" onclick="updateKategoriIncome(\'' . $item->uuid . '\')">
                            Edit
                        </a>
                        
                        <a href="javascript:void(0)" class="btn btn-sm btn-danger text-white" onclick="deleteKategoriIncome(\'' . $item->uuid . '\')">
                            Delete
                        </a>
                    ';
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('v2.admin.category.income.index');
    }

    public function create()
    {
    }

    public function store(CategoryIncomeRequest $request)
    {
        $categoryIncome = CategoryIncome::find($request->uuid);

        if ($categoryIncome) {
            $this->authorize('updateOrCreate', $categoryIncome);
        }
        
        $validated = $request->validated();
        $data = $this->categoryIncomeService->updateOrCreateCategoryIncome($validated);
        return response()->json($data);
    }

    public function show(Request $request)
    {
        $data = CategoryIncome::where('uuid', $request->uuid)
            ->where('users_uuid', Auth::id())
            ->firstOrFail();

        $this->authorize('view', $data);

        return response()->json($data);
    }

    public function edit(CategoryIncome $categoryIncome)
    {
    }

    public function update(Request $request, CategoryIncome $categoryIncome)
    {
    }

    public function destroy(Request $request)
    {
        $data = CategoryIncome::where('uuid', $request->uuid)
            ->where('users_uuid', Auth::id())
            ->firstOrFail();

        $this->authorize('delete', $data);
        $data->delete();
        
        // Clear cache
        Cache::forget('admin_categories_income_' . Auth::id());
        
        return response()->json([
            'status' => true,
            'message' => 'Data deleted successfully'
        ]);
    }
}
