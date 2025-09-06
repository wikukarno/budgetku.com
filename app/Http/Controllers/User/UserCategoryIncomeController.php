<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryIncomeRequest;
use App\Services\CategoryIncomeService;
use App\Models\CategoryIncome;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

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
            $query = CategoryIncome::where('users_uuid', Auth::id());

            return datatables()->of($query)
                ->addIndexColumn()
                ->addColumn('name_category_incomes_pgp', function ($item) {
                    return $item->name_category_incomes_pgp;
                })
                ->addColumn('content_key_version', function ($item) {
                    return $item->content_key_version;
                })
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
        return view('v2.user.category.income.index');
    }

    public function listAll()
    {
        $items = CategoryIncome::where('users_uuid', Auth::id())
            ->orderBy('created_at', 'DESC')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'uuid' => $item->uuid ?? null,
                    'name_category_incomes' => $item->name_category_incomes,
                    'name_category_incomes_pgp' => $item->name_category_incomes_pgp,
                    'content_key_version' => $item->content_key_version,
                    'created_at' => optional($item->created_at)->isoFormat('D MMMM Y'),
                    'updated_at' => optional($item->updated_at)->isoFormat('D MMMM Y'),
                    'action' => '
                        <a href="javascript:void(0)" class="btn btn-sm btn-warning text-white" onclick="updateKategoriIncome(\'' . ($item->uuid ?? $item->id) . '\')">Edit</a>
                        <a href="javascript:void(0)" class="btn btn-sm btn-danger text-white" onclick="deleteKategoriIncome(\'' . ($item->uuid ?? $item->id) . '\')">Delete</a>'
                ];
            });

        return response()->json($items);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreCategoryIncomeRequest $request)
    {
        $categoryIncome = CategoryIncome::find($request->uuid);

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
        $data = CategoryIncome::findOrFail($request->uuid);
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
        $data = CategoryIncome::find($request->uuid);
        $this->authorize('delete', $data);
        $data->delete();
        Cache::forget('user_categories_income_' . Auth::id());
        return response()->json(
            [
                'status' => true,
                'message' => 'Data deleted successfully',
            ]
        );
    }
}
