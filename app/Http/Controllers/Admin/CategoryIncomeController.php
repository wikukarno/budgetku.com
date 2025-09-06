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
            $query = CategoryIncome::where('users_uuid', Auth::id())->orderBy('created_at', 'DESC');

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
                    $uuid = addslashes($item->uuid);
                    return '\n                        <button class="btn btn-sm btn-warning text-white" onclick="updateKategoriIncome(\'' . $uuid . '\')">Edit</button>\n                        <button class="btn btn-sm btn-danger text-white" onclick="deleteKategoriIncome(\'' . $uuid . '\')">Delete</button>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('v2.admin.category.income.index');
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
                        <button class="btn btn-sm btn-warning text-white" onclick="updateKategoriIncome(\'' . ($item->uuid ?? '') . '\')">Edit</button>
                        <button class="btn btn-sm btn-danger text-white" onclick="deleteKategoriIncome(\'' . ($item->uuid ?? '') . '\')">Delete</button>'
                ];
            });

        return response()->json($items);
    }

    public function create()
    {
    }

    public function store(Request $request)
    {
        $payload = [
            'users_uuid' => Auth::id(),
            'name_category_incomes' => $request->name_category_incomes,
        ];
        if ($request->filled('name_category_incomes_pgp')) {
            $payload['name_category_incomes_pgp'] = $request->input('name_category_incomes_pgp');
            $payload['content_key_version'] = optional(Auth::user())->key_version ?? 1;
        }

        if ($request->filled('uuid')) {
            $data = CategoryIncome::updateOrCreate(['uuid' => $request->uuid], $payload);
        } elseif ($request->filled('id_category_income')) {
            $data = CategoryIncome::updateOrCreate(['id' => $request->id_category_income], $payload);
        } else {
            $data = CategoryIncome::create($payload);
        }

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
        $data = null;
        if ($request->filled('uuid')) {
            $data = CategoryIncome::where('uuid', $request->uuid)->first();
        } elseif ($request->filled('id')) {
            $data = CategoryIncome::where('id', $request->id)->first();
        }

        if (!$data) {
            return response()->json(['message' => 'Not found'], 404);
        }

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
        if ($request->filled('uuid')) {
            $data = CategoryIncome::where('uuid', $request->uuid)->first();
        } else {
            $data = CategoryIncome::find($request->id);
        }
        $data->delete();

        return response()->json($data);
    }
}
