<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\About;
use Illuminate\Http\Request;

class AboutsController extends Controller
{
    public function all(Request $request)
    {
        $about = About::all();
        if ($about) {
            return ResponseFormatter::success(
                $about,
                'Data tentang berhasil diambil'
            );
        } else {
            return ResponseFormatter::error(
                null,
                'Data tentang tidak ada',
                404
            );
        }
    }
}
