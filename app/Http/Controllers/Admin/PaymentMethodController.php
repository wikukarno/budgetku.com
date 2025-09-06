<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use App\Services\Admin\PaymentMethodService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
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
        if (request()->has('draw')) {
            $query = $this->paymentMethodService->getAllPaymentMethods();

            return datatables()->of($query)
                ->addIndexColumn()
                ->addColumn('name_pgp', fn($item) => $item->name_pgp)
                ->editColumn('name', fn($item) => $item->name)
                ->editColumn('created_at', fn($item) => Carbon::parse($item->created_at)->isoFormat('D MMMM Y'))
                ->editColumn('updated_at', fn($item) => Carbon::parse($item->updated_at)->isoFormat('D MMMM Y'))
                ->editColumn('action', function ($item) {
                    $uuid = addslashes($item->uuid);
                    return "\n                        <button class=\"btn btn-sm btn-warning text-white\" onclick=\"btnEditPaymentMethod('{$uuid}')\">Edit</button>\n                        <button class=\"btn btn-sm btn-danger text-white\" onclick=\"btnDeletePaymentMethod('{$uuid}')\">Delete</button>";
                })
                ->rawColumns(['action', 'created_at', 'updated_at'])
                ->make(true);
        }

        return view('v2.admin.payment-method.index');
    }

    public function listAll()
    {
        $items = PaymentMethod::where('users_uuid', auth()->id())->orderBy('created_at', 'DESC')->get();
        $data = collect($items)->map(function ($item) {
            return [
                'uuid' => $item->uuid ?? null,
                'name' => $item->name,
                'name_pgp' => $item->name_pgp,
                'content_key_version' => $item->content_key_version,
                'created_at' => optional($item->created_at)->isoFormat('D MMMM Y'),
                'updated_at' => optional($item->updated_at)->isoFormat('D MMMM Y'),
                'action' => "\n                        <button class=\"btn btn-sm btn-warning text-white\" onclick=\"btnEditPaymentMethod('" . ($item->uuid ?? '') . "')\">Edit</button>\n                        <button class=\"btn btn-sm btn-danger text-white\" onclick=\"btnDeletePaymentMethod('" . ($item->uuid ?? '') . "')\">Delete</button>"
            ];
        });
        return response()->json($data);
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
            'uuid' => 'nullable|string',
            'name' => 'nullable|string|max:255',
            'name_pgp' => 'nullable|string',
        ]);

        $payload = [
            'users_uuid' => auth()->id(),
            'users_id' => optional(auth()->user())->id,
            'name' => $request->name,
        ];
        if ($request->filled('name_pgp')) {
            $payload['name_pgp'] = $request->name_pgp;
            $payload['content_key_version'] = optional(auth()->user())->key_version ?? 1;
            $payload['name'] = $request->name ?: '[encrypted]';
        }

        if ($request->filled('uuid')) {
            $pm = PaymentMethod::updateOrCreate(['uuid' => $request->uuid], $payload);
        } else {
            $pm = PaymentMethod::create($payload);
        }

        Cache::forget('payment_methods');
        return response()->json(['status' => true, 'message' => 'Saved']);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {
        $pm = null;
        if ($request->filled('uuid')) {
            $pm = PaymentMethod::where('uuid', $request->uuid)->where('users_uuid', auth()->id())->first();
        } elseif ($request->filled('id')) {
            $pm = PaymentMethod::where('id', $request->id)->where(function($q){
                $q->where('users_uuid', auth()->id())->orWhere('users_id', auth()->user()->id ?? 0);
            })->first();
        }
        if ($pm) {
            return response()->json(['status' => true, 'data' => $pm]);
        }
        return response()->json(['status' => false, 'message' => 'Payment method not found.']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        try {
            $pm = null;
            if ($request->filled('uuid')) {
                $pm = PaymentMethod::where('uuid', $request->uuid)->where('users_uuid', auth()->id())->firstOrFail();
            } elseif ($request->filled('id')) {
                $pm = PaymentMethod::where('id', $request->id)->where(function($q){
                    $q->where('users_uuid', auth()->id())->orWhere('users_id', auth()->user()->id ?? 0);
                })->firstOrFail();
            } else {
                return response()->json(['status' => false, 'message' => 'Invalid identifier.'], 422);
            }

            // Admin area already gated by 'owner' middleware; skip policy check to match category controllers
            $pm->delete();
            Cache::forget('payment_methods');
            return response()->json(['status' => true, 'message' => 'Payment method deleted successfully.']);
        } catch (\Exception $e) {
            // Log the error message for debugging
            Log::error('Payment method deletion failed: ' . $e->getMessage());
            return response()->json(['status' => false, 'message' => 'Failed to delete payment method.']);
        }
    }
}
