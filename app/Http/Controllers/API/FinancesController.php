<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Finance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FinancesController extends Controller
{
    public function getFinances($id)
    {
        $finance = Finance::where('users_id', $id)->get();

        if ($finance && $finance->count() > 0) {
            return ResponseFormatter::success(
                $finance,
                'Data Finance berhasil diambil'
            );
        } else {
            return ResponseFormatter::error(
                null,
                'Data Finance tidak ada',
                404
            );
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'users_id' => 'required',
            'name_item' => 'required',
            'category' => 'required',
            'price' => 'required',
            'purchase_date' => 'required',
            'purchase_by' => 'required',
        ]);

        $finance = Finance::create([
            'users_id' => Auth::user()->id,
            'name_item' => $request->name_item,
            'category' => $request->category,
            'price' => $request->price,
            'purchase_date' => $request->purchase_date,
            'purchase_by' => $request->purchase_by,
        ]);

        if ($finance) {
            return response()->json([
                'success' => true,
                'message' => 'Data Finance berhasil disimpan',
                'data' => $finance
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Data Finance gagal disimpan',
                'data' => ''
            ], 409);
        }
    }
}
