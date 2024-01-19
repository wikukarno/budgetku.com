<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Salary;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Cookie;

class SalaryController extends Controller
{
    public function fetch(Request $request)
    {
        try {

            // Memastikan bahwa pengguna terautentikasi
            if (!auth()->check()) {
                return response()->json(['message' => 'Unauthorized - User not authenticated'], 401);
            }

            $userId = auth()->user()->id;

            $salary = Salary::where('users_id', $userId)->get();
            if ($salary->isEmpty()) {
                return response()->json(['message' => 'No salary data found'], 404);
            }

            return response()->json([
                'message' => 'Success',
                'data' => $salary
            ], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
}
