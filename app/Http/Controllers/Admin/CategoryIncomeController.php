<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CategoryIncome;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoryIncomeController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            $query = CategoryIncome::where('users_id', Auth::id())->orderBy('created_at', 'DESC');

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
                        <button class="btn btn-sm btn-warning" onclick="updateKategoriIncome(' . $item->id . ')">
                            Edit
                        </button>


                        <button class="btn btn-sm btn-danger" onclick="deleteKategoriIncome(' . $item->id . ')">
                            Delete
                        </button>
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

    public function store(Request $request)
    {
        $data = CategoryIncome::updateOrCreate(
            [
                'id' => $request->id_category_income,
            ],
            [
                'users_id' => Auth::id(),
                'name_category_incomes' => $request->name_category_incomes,
            ]
        );

        // if the data is successfully created
        if ($data->wasRecentlyCreated) {
            return response()->json([
                'status' => true,
                'message' => 'Data berhasil ditambahkan',
            ]);
        } elseif ($data->wasChanged()) {
            return response()->json([
                'status' => true,
                'message' => 'Data berhasil diubah',
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Data gagal ditambahkan',
            ]);
        }
    }

    public function show(CategoryIncome $categoryIncome, Request $request)
    {
        $data = CategoryIncome::find($request->id);
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
        $data = CategoryIncome::find($request->id);
        $data->delete();

        return response()->json($data);
    }
}
