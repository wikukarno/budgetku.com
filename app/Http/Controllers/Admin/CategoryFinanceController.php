<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryFinanceRequest;
use App\Models\CategoryFinance;
use Illuminate\Http\Request;

class CategoryFinanceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (request()->ajax()) {
            $query = CategoryFinance::query();

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
                            <button type="button" class="btn btn-warning">Edit</button>
                        </a>
                        
                        <a href="javascript:void(0)" onclick="deleteKategoriFinance(' . $item->id . ')">
                            <button type="button" class="btn btn-danger">Delete</button>
                        </a>
                    ';
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('admin.kategori-finance.index');
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
    public function store(CategoryFinanceRequest $request)
    {
        $data = CategoryFinance::updateOrCreate(
            [
                'id' => $request->id_category_finance
            ],
            [
                'name_category_finances' => $request->name_category_finances,
            ]
        );

        if ($data) {
            return redirect()->route('category-finance.index')->with('success', 'Data berhasil disimpan');
        } else {
            return redirect()->route('category-finance.index')->with('error', 'Data gagal disimpan');
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
        $data = CategoryFinance::find($request->id);
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
        $data->delete();
        return response()->json($data);
    }
}
