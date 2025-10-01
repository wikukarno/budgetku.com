<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessUangKeluarEmail;
use App\Models\CategoryFinance;
use App\Models\Finance;
use App\Models\PaymentMethod;
use App\Helpers\NotificationHelper;
use App\Helpers\CacheHelper;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

abstract class AbstractFinanceController extends Controller
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
                ->editColumn('category_finances_uuid', fn($item) => $item->category_finance->name_category_finances)
                ->editColumn('price', fn($item) => 'Rp.' . number_format($item->price, 0, ',', '.'))
                ->editColumn('purchase_date', fn($item) => Carbon::parse($item->purchase_date)->isoFormat('D MMMM Y'))
                ->editColumn('action', fn($item) => $this->buildActionButtons($item))
                ->rawColumns(['action', 'purchase_date', 'price'])
                ->make(true);
        }

        return view($this->getViewPath() . '.index');
    }

    public function create()
    {
        $categories = $this->getCachedCategoryFinances();
        $paymentMethods = $this->getCachedPaymentMethods();
        return view($this->getViewPath() . '.create', compact('categories', 'paymentMethods'));
    }

    public function store(Request $request)
    {
        $this->validateFinanceRequest($request);

        try {
            $finance = $this->createFinanceRecord($request);
            $this->clearRelatedCaches();
            $this->dispatchEmailJob($finance);

            return response()->json([
                'status' => true, 
                'message' => 'Data Created Successfully'
            ]);
        } catch (\Throwable $e) {
            Log::error('Error creating expense: ' . $e->getMessage());
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
            Finance::find($request->uuid);
        
        return response()->json($data);
    }

    public function edit($id)
    {
        $data = $this->getFinanceForEdit($id);
        $categories = $this->getCachedCategoryFinances();
        $paymentMethods = $this->getCachedPaymentMethods();
        $data->price = 'Rp. ' . number_format($data->price, 0, ',', '.');
        
        return view($this->getViewPath() . '.edit', compact('data', 'categories', 'paymentMethods'));
    }

    public function update(Request $request, $id)
    {
        $this->validateFinanceRequest($request);

        try {
            $finance = $this->getFinanceById($id);
            $this->authorize('update', $finance);

            if ($request->purchase_date > Carbon::now()->format('Y-m-d')) {
                throw new \Exception('Tanggal tidak boleh melebihi tanggal sekarang');
            }

            $this->updateFinanceRecord($finance, $request);
            $this->clearRelatedCaches();
            $this->dispatchEmailJob($finance);

            return response()->json([
                'status' => true, 
                'message' => 'Data Updated Successfully'
            ]);
        } catch (\Throwable $e) {
            Log::error('Error updating expense: ' . $e->getMessage());
            return response()->json([
                'status' => false, 
                'message' => 'Data Failed to Update'
            ]);
        }
    }

    public function destroy(Request $request)
    {
        try {
            $finance = $this->getFinanceById($request->uuid);
            $this->authorize('delete', $finance);

            $this->deleteFinanceRecord($finance);
            $this->clearRelatedCaches();

            return response()->json([
                'status' => true, 
                'message' => 'Data successfully deleted'
            ]);
        } catch (\Throwable $e) {
            Log::error('Error deleting expense: ' . $e->getMessage());
            return response()->json([
                'status' => false, 
                'message' => 'Data Failed to Delete'
            ]);
        }
    }

    protected function getDatatableQuery()
    {
        if ($this->service && method_exists($this->service, 'getDatatableQuery')) {
            return $this->service->getDatatableQuery();
        }
        
        return Finance::with('category_finance')
            ->where('users_uuid', Auth::id())
            ->orderBy('created_at', 'DESC');
    }

    protected function buildActionButtons($item): string
    {
        $editRoute = route($this->getRouteName() . '.edit', $item->uuid);
        
        return '
            <a href="' . $editRoute . '" class="btn btn-sm btn-warning text-white">Edit</a>
            <a href="javascript:void(0)" class="btn btn-sm btn-danger text-white" onclick="deleteExpense(\'' . $item->uuid . '\')">Delete</a>
        ';
    }

    protected function validateFinanceRequest(Request $request): void
    {
        $request->validate([
            'category_finances_uuid' => 'required|exists:category_finances,uuid',
            'name_item' => 'required|string|max:255',
            'price' => 'required|string',
            'purchase_date' => 'required|date',
            'payment_methods_uuid' => 'required|exists:payment_methods,uuid',
            'bukti_pembayaran' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);
    }

    protected function getCachedCategoryFinances()
    {
        return Cache::remember(
            'user_categories_finance_' . Auth::id(), 
            3600, 
            fn() => CategoryFinance::where('users_uuid', Auth::id())->get()
        );
    }

    protected function getCachedPaymentMethods()
    {
        return Cache::remember(
            'payment_methods', 
            3600, 
            fn() => PaymentMethod::select('uuid', 'name')->get()
        );
    }

    protected function createFinanceRecord(Request $request): Finance
    {
        if ($this->service && method_exists($this->service, 'create')) {
            return $this->service->create($request->all());
        }

        $finance = Finance::create([
            'users_uuid' => Auth::id(),
            'category_finances_uuid' => $request->category_finances_uuid,
            'name_item' => $request->name_item,
            'price' => $this->formatPriceForStorage($request->price),
            'purchase_date' => $request->purchase_date ?? Carbon::now(),
            'payment_methods_uuid' => $request->payment_methods_uuid,
            'bukti_pembayaran' => null,
        ]);

        if ($request->hasFile('bukti_pembayaran')) {
            $file = $request->file('bukti_pembayaran')->store('assets/bukti_pembayaran', 'public');
            $finance->update(['bukti_pembayaran' => $file]);
        }

        return $finance;
    }

    protected function updateFinanceRecord(Finance $finance, Request $request): void
    {
        if ($this->service && method_exists($this->service, 'update')) {
            $this->service->update($finance->uuid, $request->all());
            return;
        }

        $finance->update([
            'users_uuid' => Auth::id(),
            'category_finances_uuid' => $request->category_finances_uuid,
            'name_item' => $request->name_item,
            'price' => $this->formatPriceForStorage($request->price),
            'purchase_date' => $request->purchase_date,
            'payment_methods_uuid' => $request->payment_methods_uuid,
        ]);

        if ($request->hasFile('bukti_pembayaran')) {
            if ($finance->bukti_pembayaran && Storage::disk('public')->exists($finance->bukti_pembayaran)) {
                Storage::disk('public')->delete($finance->bukti_pembayaran);
            }

            $file = $request->file('bukti_pembayaran')->store('assets/bukti_pembayaran', 'public');
            $finance->update(['bukti_pembayaran' => $file]);
        }
    }

    protected function deleteFinanceRecord(Finance $finance): void
    {
        if ($this->service && method_exists($this->service, 'delete')) {
            $this->service->delete($finance->uuid);
            return;
        }

        if ($finance->bukti_pembayaran && Storage::disk('public')->exists($finance->bukti_pembayaran)) {
            Storage::disk('public')->delete($finance->bukti_pembayaran);
        }

        $finance->delete();
    }

    protected function getFinanceById($id): Finance
    {
        if ($this->service && method_exists($this->service, 'getById')) {
            return $this->service->getById($id);
        }

        return Finance::findOrFail($id);
    }

    protected function getFinanceForEdit($id): Finance
    {
        if ($this->service && method_exists($this->service, 'getByUser')) {
            return $this->service->getByUser($id);
        }

        return Finance::where('users_uuid', Auth::id())->findOrFail($id);
    }

    protected function formatPriceForStorage(string $price): string
    {
        // Remove currency prefix and handle both Indonesian and international formats
        $cleaned = str_replace(['Rp. ', 'Rp ', 'IDR ', '$'], '', $price);
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

    protected function dispatchEmailJob(Finance $finance): void
    {
        $user = Auth::user();
        
        NotificationHelper::dispatchEmailIfEnabled(
            $user,
            function () use ($finance, $user) {
                ProcessUangKeluarEmail::dispatch([
                    'finance' => $finance,
                    'user' => $user,
                    'saldo' => $this->calculateUserBalance()
                ]);
            },
            'expense'
        );
    }

    protected function calculateUserBalance(): int
    {
        $userId = Auth::id();
        $salary = \App\Models\Salary::where('users_uuid', $userId)->sum('salary');
        $expenses = Finance::where('users_uuid', $userId)->sum('price');
        
        return $salary - $expenses;
    }

    protected function clearRelatedCaches(): void
    {
        CacheHelper::clearDashboardCaches();
        Cache::forget('user_categories_finance_' . Auth::id());
    }
}