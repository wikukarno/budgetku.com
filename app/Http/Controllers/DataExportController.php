<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessDataExport;
use App\Models\DataExport;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DataExportController extends Controller
{
    public function index()
    {
        $exports = DataExport::where('users_uuid', Auth::id())
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        return view('v2.data-export.index', compact('exports'));
    }

    public function request()
    {
        $user = Auth::user();

        // Prevent spam: check if there's already a pending/processing export
        $activeExport = DataExport::where('users_uuid', $user->uuid)
            ->whereIn('status', ['pending', 'processing'])
            ->first();

        if ($activeExport) {
            return redirect()->back()->with('warning', 'You already have an export in progress. Please wait until it finishes.');
        }

        $export = DataExport::create([
            'users_uuid' => $user->uuid,
            'status' => 'pending',
        ]);

        ProcessDataExport::dispatch($export->uuid, $user->uuid);

        return redirect()->route($this->getRouteName('index'))
            ->with('success', 'Your data export has been queued. You will be able to download it shortly.');
    }

    public function status(string $uuid)
    {
        $export = DataExport::where('uuid', $uuid)
            ->where('users_uuid', Auth::id())
            ->firstOrFail();

        return response()->json([
            'status' => $export->status,
            'file_name' => $export->file_name,
            'file_size' => $export->file_size ? $this->formatBytes($export->file_size) : null,
            'completed_at' => $export->completed_at?->diffForHumans(),
            'expires_at' => $export->expires_at?->diffForHumans(),
            'error_message' => $export->error_message,
        ]);
    }

    public function download(string $uuid)
    {
        $export = DataExport::where('uuid', $uuid)
            ->where('users_uuid', Auth::id())
            ->where('status', 'completed')
            ->firstOrFail();

        if ($export->isExpired()) {
            return redirect()->back()->with('error', 'This export has expired. Please request a new one.');
        }

        $fullPath = storage_path("app/{$export->file_path}");

        if (!file_exists($fullPath)) {
            return redirect()->back()->with('error', 'Export file not found. Please request a new one.');
        }

        return response()->download($fullPath, $export->file_name);
    }

    protected function getRouteName(string $action): string
    {
        $user = Auth::user();

        if ($user->roles === 'Owner') {
            return "admin.data.export.{$action}";
        }

        return "customer.data.export.{$action}";
    }

    protected function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        return round($bytes, 2) . ' ' . $units[$i];
    }
}
