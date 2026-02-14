<div id="announcement-banner" class="alert alert-info alert-dismissible fade show d-flex align-items-center mb-3 rounded-15 shadow-sm" role="alert" style="border: none; background: linear-gradient(135deg, #4a90d9 0%, #667eea 100%); color: #fff;">
    <div class="d-flex align-items-center w-100">
        <div class="flex-shrink-0 me-3">
            <i class="material-symbols-outlined" style="font-size: 28px;">download</i>
        </div>
        <div class="flex-grow-1">
            <strong>Download Your Data</strong>
            <p class="mb-0 mt-1" style="font-size: 14px; opacity: 0.9;">
                You can now export and download your transaction data directly from your account. Keep your records safe!
            </p>
        </div>
        <div class="flex-shrink-0 ms-3 d-none d-sm-block">
            @if (Auth::user()->roles == "Owner")
                <a href="{{ route('admin.data.export.index') }}" class="btn btn-sm btn-light fw-medium" style="border-radius: 8px;">
                    <i class="material-symbols-outlined align-middle" style="font-size: 18px;">download</i>
                    Download Now
                </a>
            @else
                <a href="{{ route('customer.data.export.index') }}" class="btn btn-sm btn-light fw-medium" style="border-radius: 8px;">
                    <i class="material-symbols-outlined align-middle" style="font-size: 18px;">download</i>
                    Download Now
                </a>
            @endif
        </div>
    </div>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close" onclick="dismissAnnouncement()"></button>
</div>

<script>
    function dismissAnnouncement() {
        localStorage.setItem('announcement_data_download_dismissed', 'true');
    }

    document.addEventListener('DOMContentLoaded', function () {
        if (localStorage.getItem('announcement_data_download_dismissed') === 'true') {
            const banner = document.getElementById('announcement-banner');
            if (banner) {
                banner.remove();
            }
        }
    });
</script>
