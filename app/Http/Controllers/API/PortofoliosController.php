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

            // Jika tidak ada token, kembalikan error 404
            if (!$token) {
                return abort(404, 'Token tidak valid');
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
            // Tangkap kesalahan lainnya dan kembalikan error 404
            return abort(404, $th->getMessage());
        }
    }
}
