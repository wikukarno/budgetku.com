<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use App\Services\Admin\PaymentMethodService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentMethodController extends Controller
{

    protected $paymentMethodService;

    public function __construct(PaymentMethodService $paymentMethodService)
    {
        $this->paymentMethodService = $paymentMethodService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        
        if (request()->ajax()) {
            $query = $this->paymentMethodService->getAllPaymentMethods();

            return datatables()->of($query)
                ->addIndexColumn()
                ->editColumn('name', fn($item) => $item->name)
                ->editColumn('created_at', fn($item) => Carbon::parse($item->created_at)->isoFormat('D MMMM Y'))
                ->editColumn('updated_at', fn($item) => Carbon::parse($item->updated_at)->isoFormat('D MMMM Y'))
                ->editColumn('action', function ($item) {
                    return '
                        <a href="javascript:void(0)" class="btn btn-sm btn-warning text-white" onclick="btnEditPaymentMethod(' . $item->id . ')">Edit</a>
                        <a href="javascript:void(0)" class="btn btn-sm btn-danger text-white" onclick="btnDeletePaymentMethod(' . $item->id . ')">Delete</a>';
                })
                ->rawColumns(['action', 'created_at', 'updated_at'])
                ->make(true);
        }

        return view('v2.admin.payment-method.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        try {
            if ($request->id) {
                $paymentMethod = $this->paymentMethodService->getPaymentMethodById($request->id);

                // Authorize update
                $this->authorize('updateOrCreate', $paymentMethod);
            } else {
                // Authorize create
                $this->authorize('create', PaymentMethod::class);
            }

            $data = $this->paymentMethodService->createPaymentMethod(
                $request->all(),
                $request->id ?? null
            );

            return response()->json([
                'status' => true,
                'message' => $request->id
                    ? 'Payment method updated successfully.'
                    : 'Payment method created successfully.'
            ]);
        } catch (\Exception $e) {
            Log::error('Payment method creation failed: ' . $e->getMessage());
            return response()->json(['status' => false, 'message' => 'Failed to create payment method.']);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {
        $request->validate([
            'id' => 'required|integer|exists:payment_methods,id',
        ]);
        $paymentMethod = $this->paymentMethodService->getPaymentMethodById($request->id);
        if ($paymentMethod) {
            return response()->json(['status' => true, 'data' => $paymentMethod]);
        } else {
            return response()->json(['status' => false, 'message' => 'Payment method not found.']);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        try {
            $paymentMethod = $this->paymentMethodService->getPaymentMethodById($request->id);
            $this->authorize('delete', $paymentMethod);
            $this->paymentMethodService->deletePaymentMethod($request->id);
            return response()->json(['status' => true, 'message' => 'Payment method deleted successfully.']);
        } catch (\Exception $e) {
            // Log the error message for debugging
            Log::error('Payment method deletion failed: ' . $e->getMessage());
            return response()->json(['status' => false, 'message' => 'Failed to delete payment method.']);
        }
    }
}
