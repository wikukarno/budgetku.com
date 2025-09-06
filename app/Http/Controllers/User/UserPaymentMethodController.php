<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class UserPaymentMethodController extends Controller
{
    public function listAll()
    {
        $items = PaymentMethod::where('users_uuid', Auth::id())->orderBy('created_at', 'DESC')->get();
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

    public function show(Request $request)
    {
        $pm = null;
        if ($request->filled('uuid')) {
            $pm = PaymentMethod::where('uuid', $request->uuid)->where('users_uuid', Auth::id())->first();
        }
        if ($pm) return response()->json(['status' => true, 'data' => $pm]);
        return response()->json(['status' => false, 'message' => 'Payment method not found.']);
    }

    public function store(Request $request)
    {
        $request->validate([
            'uuid' => 'nullable|string',
            'name' => 'nullable|string|max:255',
            'name_pgp' => 'nullable|string',
        ]);

        try {
            $payload = [
                'users_uuid' => Auth::id(),
                'users_id' => optional(Auth::user())->id,
                'name' => $request->name,
            ];
            if ($request->filled('name_pgp')) {
                $payload['name_pgp'] = $request->name_pgp;
                $payload['content_key_version'] = optional(Auth::user())->key_version ?? 1;
                $payload['name'] = $request->name ?: '[encrypted]';
            }

            if ($request->filled('uuid')) {
                PaymentMethod::updateOrCreate(['uuid' => $request->uuid, 'users_uuid' => Auth::id()], $payload);
            } else {
                PaymentMethod::create($payload);
            }

            Cache::forget('payment_methods');
            return response()->json(['status' => true, 'message' => 'Saved']);
        } catch (\Throwable $e) {
            Log::error('User PM store failed: '.$e->getMessage());
            return response()->json(['status' => false, 'message' => 'Failed to save'], 500);
        }
    }

    public function destroy(Request $request)
    {
        try {
            $pm = null;
            if ($request->filled('uuid')) {
                $pm = PaymentMethod::where('uuid', $request->uuid)->where('users_uuid', Auth::id())->firstOrFail();
            }
            $pm->delete();
            Cache::forget('payment_methods');
            return response()->json(['status' => true, 'message' => 'Deleted']);
        } catch (\Throwable $e) {
            Log::error('User PM delete failed: '.$e->getMessage());
            return response()->json(['status' => false, 'message' => 'Failed to delete']);
        }
    }
}

