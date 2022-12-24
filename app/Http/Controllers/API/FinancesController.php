<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FinancesController extends Controller
{
    public function getFinances(Request $request)
    {
        return response()->json([
            'message' => 'Hello World',
        ]);
    }
}
