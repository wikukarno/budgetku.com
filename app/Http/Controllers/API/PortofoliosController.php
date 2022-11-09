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
        $portofolio = Portofolio::all();
        if ($portofolio) {
            return ResponseFormatter::success(
                $portofolio,
                'Data portofolio berhasil diambil'
            );
        } else {
            return ResponseFormatter::error(
                null,
                'Data portofolio tidak ada',
                404
            );
        }
    }
}
