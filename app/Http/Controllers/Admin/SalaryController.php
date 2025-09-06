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

    public function listAll()
    {
        $items = \App\Models\Salary::with(['category_income', 'legacyCategoryIncome'])
            ->where('users_uuid', Auth::id())
            ->orderBy('created_at', 'DESC')
            ->get()
            ->map(function ($item) {
                $cat = $item->category_income ?: $item->legacyCategoryIncome;
                return [
                    'uuid' => $item->uuid,
                    'category_incomes_uuid' => $item->category_incomes_uuid ?: ($cat ? $cat->uuid : null),
                    'category_name' => optional($cat)->name_category_incomes,
                    'category_name_pgp' => optional($cat)->name_category_incomes_pgp,
                    'category_key_version' => optional($cat)->content_key_version,
                    'salary' => $item->salary,
                    'salary_pgp' => $item->salary_pgp,
                    'salary_fmt' => $item->salary === '[encrypted]' ? '[encrypted]' : ('Rp. ' . number_format((int)$item->salary, 0, ',', '.')),
                    // Ensure date field is always a parseable string
                    'date' => optional($item->date) ? Carbon::parse($item->date)->format('Y-m-d') : null,
                    'date_human' => optional($item->date ? Carbon::parse($item->date) : null)->isoFormat('D MMMM Y'),
                    'description' => $item->description,
                    'description_pgp' => $item->description_pgp,
                    'content_key_version' => $item->content_key_version,
                    'action' => '<a href="/pages/admin/income/edit/' . $item->uuid . '" class="btn btn-sm btn-warning text-white">Edit</a> '
                        . '<a href="javascript:void(0)" class="btn btn-sm btn-danger text-white" onclick="deleteIncome(\'' . $item->uuid . '\')">Delete</a>',
                ];
            });

        return response()->json($items);
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
            'salary_pgp' => 'nullable|string',
            'date' => 'required|date',
            'category_incomes_uuid' => 'required|exists:category_incomes,uuid',
            'description' => 'required|string',
            'description_pgp' => 'nullable|string',
        ]);

        try {
            $payload = [
                'users_uuid' => Auth::id(),
                'salary' => str_replace(['Rp. ', '.'], ['', ''], $request->salary),
                'date' => $request->date,
                'category_incomes_uuid' => $request->category_incomes_uuid,
                'description' => $request->description,
            ];
            if ($request->filled('salary_pgp')) {
                $payload['salary'] = '[encrypted]';
                $payload['salary_pgp'] = $request->salary_pgp;
                $payload['content_key_version'] = optional(Auth::user())->key_version ?? 1;
            }
            if ($request->filled('description_pgp')) {
                $payload['description'] = '[encrypted]';
                $payload['description_pgp'] = $request->description_pgp;
                $payload['content_key_version'] = optional(Auth::user())->key_version ?? 1;
            }

            $salary = \App\Models\Salary::create($payload);

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
            'salary_pgp' => 'nullable|string',
            'date' => 'required|date',
            'category_incomes_uuid' => 'required|exists:category_incomes,uuid',
            'description' => 'required|string',
            'description_pgp' => 'nullable|string',
        ]);

        try {
            // Update by UUID from route param
            $data = \App\Models\Salary::where('uuid', $id)->firstOrFail();
            $this->authorize('update', $data);

            if ($request->date > Carbon::now()->format('Y-m-d')) {
                Log::error('Tanggal tidak boleh melebihi tanggal sekarang');
                return response()->json(['status' => false, 'message' => 'Invalid date'], 422);
            }

            $data->users_uuid = Auth::id();
            if ($request->filled('salary_pgp')) {
                $data->salary = '[encrypted]';
                $data->salary_pgp = $request->salary_pgp;
                $data->content_key_version = optional(Auth::user())->key_version ?? 1;
            } else {
                // Normalize currency string (handle 'Rp.' and 'Rp. ')
                $data->salary = str_replace(['Rp. ', 'Rp.', '.', ','], ['', '', '', ''], $request->salary);
            }
            $data->date = $request->date;
            $data->category_incomes_uuid = $request->category_incomes_uuid;
            if ($request->filled('description_pgp')) {
                $data->description = '[encrypted]';
                $data->description_pgp = $request->description_pgp;
                $data->content_key_version = optional(Auth::user())->key_version ?? 1;
            } else {
                $data->description = $request->description;
            }
            $data->save();

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

            return response()->json(['code' => 200, 'message' => 'Data successfully deleted']);
        } catch (\Throwable $th) {
            Log::error($th);
            return response()->json(['code' => 500, 'message' => 'Data failed to delete']);
        }
    }
}
