<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Document;
use Illuminate\Http\Request;

class DocumentController extends Controller
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
            $document = Document::first();
            if ($document) {
                return ResponseFormatter::success(
                    $document,
                    'Data dokumen berhasil diambil'
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
