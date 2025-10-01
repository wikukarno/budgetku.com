<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessUangMasukEmail;
use App\Models\CategoryIncome;
use App\Models\Salary;
use App\Helpers\NotificationHelper;
use App\Helpers\CacheHelper;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

abstract class AbstractIncomeController extends Controller
{
    protected $service;

    public function __construct()
    {
        $this->service = $this->getService();
    }

    abstract protected function getService();
    abstract protected function getViewPath(): string;
    abstract protected function getRouteName(): string;

    public function index()
    {
        if (request()->ajax()) {
            $query = $this->getDatatableQuery();

            return datatables()->of($query)
                ->addIndexColumn()
                ->editColumn('category_incomes_uuid', fn($item) => $item->category_income->name_category_incomes)
                ->editColumn('salary', fn($item) => 'Rp.' . number_format($item->salary, 0, ',', '.'))
                ->editColumn('date', fn($item) => Carbon::parse($item->date)->isoFormat('D MMMM Y'))
                ->editColumn('action', fn($item) => $this->buildActionButtons($item))
                ->rawColumns(['action', 'date', 'salary', 'tipe'])
                ->make(true);
        }

        return view($this->getViewPath() . '.index');
    }

    public function create()
    {
        $categoryIncome = $this->getCachedCategoryIncomes();
        return view($this->getViewPath() . '.create', compact('categoryIncome'));
    }

    public function store(Request $request)
    {
        $this->validateIncomeRequest($request);

        try {
            $salary = $this->createIncomeRecord($request);
            $this->clearRelatedCaches();
            $this->dispatchEmailJob($salary);

            return response()->json([
                'status' => true, 
                'message' => 'Data Created Successfully'
            ]);
        } catch (\Throwable $e) {
            Log::error('Error creating income: ' . $e->getMessage());
            return response()->json([
                'status' => false, 
                'message' => 'Data Failed to Create'
            ]);
        }
    }

    public function show(Request $request)
    {
        $data = $this->service ? 
            $this->service->getById($request->uuid) : 
            Salary::find($request->uuid);
        
        return response()->json($data);
    }

    public function edit($id)
    {
        $data = $this->getIncomeForEdit($id);
        $categoryIncome = $this->getCachedCategoryIncomes();
        $data->salary = 'Rp. ' . number_format($data->salary, 0, ',', '.');
        
        return view($this->getViewPath() . '.edit', compact('data', 'categoryIncome'));
    }

    public function update(Request $request, $id)
    {
        $this->validateIncomeRequest($request);

        try {
            $salary = $this->getIncomeById($id);
            $this->authorize('update', $salary);

            if ($request->date > Carbon::now()->format('Y-m-d')) {
                throw new \Exception('Tanggal tidak boleh melebihi tanggal sekarang');
            }

            $this->updateIncomeRecord($salary, $request);
            $this->clearRelatedCaches();

            return response()->json([
                'status' => true, 
                'message' => 'Data Updated Successfully'
            ]);
        } catch (\Throwable $e) {
            Log::error('Error updating income: ' . $e->getMessage());
            return response()->json([
                'status' => false, 
                'message' => 'Data Failed to Update'
            ]);
        }
    }

    public function destroy(Request $request)
    {
        try {
            $salary = $this->getIncomeById($request->uuid);
            $this->authorize('delete', $salary);

            $this->deleteIncomeRecord($salary);
            $this->clearRelatedCaches();

            return response()->json([
                'code' => 200, 
                'message' => 'Data successfully deleted'
            ]);
        } catch (\Throwable $e) {
            Log::error('Error deleting income: ' . $e->getMessage());
            return response()->json([
                'code' => 500, 
                'message' => 'Data Failed to Delete'
            ]);
        }
    }

    protected function getDatatableQuery()
    {
        if ($this->service && method_exists($this->service, 'getDatatableQuery')) {
            return $this->service->getDatatableQuery();
        }
        
        return Salary::with('category_income')
            ->where('users_uuid', Auth::id())
            ->orderBy('created_at', 'DESC');
    }

    protected function buildActionButtons($item): string
    {
        $editRoute = route($this->getRouteName() . '.edit', $item->uuid);
        
        return '
            <a href="' . $editRoute . '" class="btn btn-sm btn-warning text-white">Edit</a>
            <a href="javascript:void(0)" class="btn btn-sm btn-danger text-white" onclick="deleteIncome(\'' . $item->uuid . '\')">Delete</a>
        ';
    }

    protected function validateIncomeRequest(Request $request): void
    {
        $request->validate([
            'salary' => 'required|string',
            'date' => 'required|date',
            'category_incomes_uuid' => 'required|exists:category_incomes,uuid',
            'description' => 'required|string',
        ]);
    }

    protected function getCachedCategoryIncomes()
    {
        return Cache::remember(
            'user_categories_income_' . Auth::id(), 
            3600, 
            fn() => CategoryIncome::where('users_uuid', Auth::id())->get()
        );
    }

    protected function createIncomeRecord(Request $request): Salary
    {
        if ($this->service && method_exists($this->service, 'create')) {
            return $this->service->create($request->all());
        }

        return Salary::create([
            'users_uuid' => Auth::id(),
            'salary' => $this->formatSalaryForStorage($request->salary),
            'date' => $request->date,
            'category_incomes_uuid' => $request->category_incomes_uuid,
            'description' => $request->description,
        ]);
    }

    protected function updateIncomeRecord(Salary $salary, Request $request): void
    {
        if ($this->service && method_exists($this->service, 'update')) {
            $this->service->update($salary->uuid, $request->all());
            return;
        }

        $salary->update([
            'users_uuid' => Auth::id(),
            'salary' => $this->formatSalaryForStorage($request->salary),
            'date' => $request->date,
            'category_incomes_uuid' => $request->category_incomes_uuid,
            'description' => $request->description,
        ]);
    }

    protected function deleteIncomeRecord(Salary $salary): void
    {
        if ($this->service && method_exists($this->service, 'delete')) {
            $this->service->delete($salary->uuid);
            return;
        }

        $salary->delete();
    }

    protected function getIncomeById($id): Salary
    {
        if ($this->service && method_exists($this->service, 'getById')) {
            return $this->service->getById($id);
        }

        return Salary::findOrFail($id);
    }

    protected function getIncomeForEdit($id): Salary
    {
        if ($this->service && method_exists($this->service, 'getByUser')) {
            return $this->service->getByUser($id);
        }

        return Salary::where('users_uuid', Auth::id())->findOrFail($id);
    }

    protected function formatSalaryForStorage(string $salary): string
    {
        // Remove currency prefix and handle both Indonesian and international formats
        $cleaned = str_replace(['Rp. ', 'Rp ', 'IDR ', '$'], '', $salary);
        $cleaned = trim($cleaned);
        
        // Indonesian format detection:
        // - If comma has 1-2 digits after it, it's decimal (123.456,50)
        // - If comma has 3+ digits after it, it's likely thousand separator error (699,115)
        if (strpos($cleaned, ',') !== false) {
            $parts = explode(',', $cleaned);
            $afterComma = $parts[1] ?? '';
            
            if (strlen($afterComma) <= 2) {
                // Decimal separator: 123.456,50 -> 123456
                $integerPart = str_replace('.', '', $parts[0]);
                $cleaned = $integerPart;
            } else {
                // Likely thousand separator error: 699,115 -> 699115
                $cleaned = str_replace(['.', ','], '', $cleaned);
            }
        } else {
            // No comma, remove dots (thousand separators): 123.456 -> 123456
            $cleaned = str_replace('.', '', $cleaned);
        }
        
        // Return clean integer string
        return preg_replace('/[^0-9]/', '', $cleaned);
    }

    protected function dispatchEmailJob(Salary $salary): void
    {
        $user = Auth::user();
        
        NotificationHelper::dispatchEmailIfEnabled(
            $user,
            function () use ($salary, $user) {
                ProcessUangMasukEmail::dispatch([
                    'salary' => $salary,
                    'user' => $user
                ]);
            },
            'income'
        );
    }

    protected function clearRelatedCaches(): void
    {
        CacheHelper::clearDashboardCaches();
    }
}