<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Document;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    public function all()
    {
        $document = Document::all();
        if ($document) {
            return ResponseFormatter::success(
                $document,
                'Data dokumen berhasil diambil'
            );
        } else {
            return ResponseFormatter::error(
                null,
                'Data dokumen tidak ada',
                404
            );
        }
    }
}
