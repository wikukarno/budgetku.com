<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SalaryRequest;
use App\Jobs\ProcessUangMasukEmail;
use App\Models\CategoryIncome;
use App\Services\Admin\SalaryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SalaryController extends Controller
{
    protected $salaryService;

    public function __construct(SalaryService $salaryService)
    {
        $this->salaryService = $salaryService;
    }

    public function index()
    {
        if (request()->ajax()) {
            $query = $this->salaryService->getDatatableQuery();

            return datatables()->of($query)
                ->addIndexColumn()
                ->editColumn('category_incomes_uuid', fn($item) => $item->category_income->name_category_incomes)
                ->editColumn('salary', fn($item) => 'Rp.' . number_format($item->salary, 0, ',', '.'))
                ->editColumn('date', fn($item) => Carbon::parse($item->date)->isoFormat('D MMMM Y'))
                ->editColumn('action', function ($item) {
                    return '
                        <a href="' . route('admin.income.edit', $item->uuid) . '" class="btn btn-sm btn-warning text-white">Edit</a>
                        <a href="javascript:void(0)" class="btn btn-sm btn-danger text-white" onclick="deleteIncome(\'' . $item->uuid . '\')">Delete</a>
                    ';
                })
                ->rawColumns(['action', 'date', 'salary', 'tipe'])
                ->make(true);
        }

        return view('v2.admin.income.index');
    }

    public function create()
    {
        $categoryIncome = CategoryIncome::where('users_uuid', Auth::id())->get();
        return view('v2.admin.income.create', compact('categoryIncome'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'salary' => 'required|string',
            'date' => 'required|date',
            'category_incomes_uuid' => 'required|exists:category_incomes,uuid',
            'description' => 'required|string',
        ]);

        try {
            Log::info('Request received:', $request->all());
            $salary = $this->salaryService->create($request->all());
            Log::info('Request received:', $request->all());

            ProcessUangMasukEmail::dispatch([
                'salary' => $salary,
                'user' => Auth::user()
            ]);

            return response()->json(['status' => true, 'message' => 'Data Created Successfully']);
        } catch (\Throwable $e) {
            Log::error($e);
            return response()->json(['status' => false, 'message' => 'Data Failed to Create']);
        }
    }

    public function show(Request $request)
    {
        $data = $this->salaryService->getById($request->uuid);
        return response()->json($data);
    }

    public function edit($id)
    {
        $data = $this->salaryService->getByUser($id);
        $categoryIncome = CategoryIncome::where('users_uuid', Auth::id())->get();
        $data->salary = 'Rp. ' . number_format($data->salary, 0, ',', '.');
        return view('v2.admin.income.edit', compact('data', 'categoryIncome'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'salary' => 'required|string',
            'date' => 'required|date',
            'category_incomes_uuid' => 'required|exists:category_incomes,uuid',
            'description' => 'required|string',
        ]);

        try {
            $salary = $this->salaryService->getById($id);
            $this->authorize('update', $salary);

            $this->salaryService->update($id, $request->all());

            return response()->json(['status' => true, 'message' => 'Data Updated Successfully']);
        } catch (\Throwable $th) {
            Log::error('Error updating data: ' . $th->getMessage());
            return response()->json(['status' => false, 'message' => 'Data Failed to Update']);
        }
    }

    public function destroy(Request $request)
    {
        try {
            $salary = $this->salaryService->getById($request->uuid);

            $this->authorize('delete', $salary);

            $this->salaryService->delete($salary->uuid);

            return response()->json(['code' => 200, 'message' => 'Data berhasil dihapus']);
        } catch (\Throwable $th) {
            Log::error($th);
            return response()->json(['code' => 500, 'message' => 'Data gagal dihapus']);
        }
    }
}
