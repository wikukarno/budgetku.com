<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\About;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AboutsController extends Controller
{
    public function all(Request $request)
    {
        try {
            $token = $request->header('Authorization');

            // check token
            if (!$token) {
                return response()->json([
                    'message' => 'Token tidak valid'
                ], 401);
            }
            
            $about = About::all();
            if ($about) {
                return ResponseFormatter::success(
                    $about,
                    'Data berhasil diambil'
                );
            } else {
                return ResponseFormatter::error(
                    null,
                    'Data tidak ada!',
                    404
                );
            }
        } catch (\Throwable $th) {
            return ResponseFormatter::error(
                null,
                'Data tidak ada!',
                404
            );
        }
    }
}
