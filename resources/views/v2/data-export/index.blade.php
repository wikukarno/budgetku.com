@extends('layouts.v2.app')

@section('title', 'Export Data')

@section('content')
    <div class="row justify-content-center">
        <div class="col-xl-8">
            <div class="card bg-white border-0 rounded-3 mb-4">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
                        <div>
                            <h3 class="mb-1">Export Your Data</h3>
                            <p class="text-muted mb-0">Download all your data including transactions, categories, and images as a ZIP file.</p>
                        </div>
                        <form method="POST" action="{{ Auth::user()->roles === 'Owner' ? route('admin.data.export.request') : route('customer.data.export.request') }}">
                            @csrf
                            <button type="submit" class="btn btn-primary d-flex align-items-center gap-2">
                                <i class="material-symbols-outlined" style="font-size: 20px;">download</i>
                                Request Export
                            </button>
                        </form>
                    </div>

                    <div class="alert alert-light border rounded-3 mb-4">
                        <h6 class="mb-2">What's included in the export:</h6>
                        <div class="row">
                            <div class="col-sm-6">
                                <ul class="mb-0 ps-3">
                                    <li>Profile information</li>
                                    <li>All expense records</li>
                                    <li>All income records</li>
                                    <li>Expense categories</li>
                                </ul>
                            </div>
                            <div class="col-sm-6">
                                <ul class="mb-0 ps-3">
                                    <li>Income categories</li>
                                    <li>Payment methods</li>
                                    <li>Bills</li>
                                    <li>All uploaded images</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    @if ($exports->isEmpty())
                        <div class="text-center py-5 text-muted">
                            <i class="material-symbols-outlined" style="font-size: 48px; opacity: 0.3;">folder_off</i>
                            <p class="mt-2 mb-0">No exports yet. Click "Request Export" to get started.</p>
                        </div>
                    @else
                        <h6 class="mb-3">Export History</h6>
                        <div class="table-responsive">
                            <table class="table align-middle">
                                <thead>
                                    <tr>
                                        <th>Status</th>
                                        <th>File</th>
                                        <th>Size</th>
                                        <th>Requested</th>
                                        <th>Expires</th>
                                        <th class="text-end">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($exports as $export)
                                        <tr id="export-row-{{ $export->uuid }}">
                                            <td>
                                                @if ($export->status === 'completed' && !$export->isExpired())
                                                    <span class="badge bg-success">Ready</span>
                                                @elseif ($export->status === 'completed' && $export->isExpired())
                                                    <span class="badge bg-secondary">Expired</span>
                                                @elseif ($export->status === 'failed')
                                                    <span class="badge bg-danger">Failed</span>
                                                @else
                                                    <span class="badge bg-warning text-dark export-status-badge">
                                                        <span class="spinner-border spinner-border-sm me-1" style="width: 12px; height: 12px;"></span>
                                                        {{ ucfirst($export->status) }}
                                                    </span>
                                                @endif
                                            </td>
                                            <td>{{ $export->file_name ?? '-' }}</td>
                                            <td>{{ $export->file_size ? number_format($export->file_size / 1048576, 2) . ' MB' : '-' }}</td>
                                            <td>{{ $export->created_at->diffForHumans() }}</td>
                                            <td>{{ $export->expires_at?->diffForHumans() ?? '-' }}</td>
                                            <td class="text-end">
                                                @if ($export->isReady())
                                                    <a href="{{ Auth::user()->roles === 'Owner' ? route('admin.data.export.download', $export->uuid) : route('customer.data.export.download', $export->uuid) }}"
                                                       class="btn btn-sm btn-primary">
                                                        <i class="material-symbols-outlined align-middle" style="font-size: 18px;">download</i>
                                                        Download
                                                    </a>
                                                @elseif ($export->status === 'failed')
                                                    <span class="text-danger small" title="{{ $export->error_message }}">Error</span>
                                                @elseif ($export->status === 'pending' || $export->status === 'processing')
                                                    <span class="text-muted small">Processing...</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('after-scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const pendingExports = @json($exports->whereIn('status', ['pending', 'processing'])->pluck('uuid'));

        if (pendingExports.length === 0) return;

        const statusRoute = "{{ Auth::user()->roles === 'Owner' ? '/pages/admin/data-export/status/' : '/pages/customer/data-export/status/' }}";
        const downloadRoute = "{{ Auth::user()->roles === 'Owner' ? '/pages/admin/data-export/download/' : '/pages/customer/data-export/download/' }}";

        function pollStatus(uuid) {
            fetch(statusRoute + uuid)
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'completed') {
                        location.reload();
                    } else if (data.status === 'failed') {
                        location.reload();
                    } else {
                        setTimeout(() => pollStatus(uuid), 3000);
                    }
                })
                .catch(() => {
                    setTimeout(() => pollStatus(uuid), 5000);
                });
        }

        pendingExports.forEach(uuid => pollStatus(uuid));
    });
</script>
@endpush
