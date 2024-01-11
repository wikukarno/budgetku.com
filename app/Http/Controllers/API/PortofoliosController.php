<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Portofolio;
use Illuminate\Http\Request;

class PortofoliosController extends Controller
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

            $portofolio = Portofolio::all();
            if ($portofolio) {
                return ResponseFormatter::success(
                    $portofolio,
                    'Data berhasil diambil!'
                );
            } else {
                return ResponseFormatter::error(
                    null,
                    'Data tidak ada!',
                    404
                );
            }
        } catch (\Throwable $th) {
            return abort(404);
        }
    }
}
