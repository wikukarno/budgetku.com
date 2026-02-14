<div id="announcement-banner" class="alert fade show d-flex align-items-center mb-3 rounded-15 shadow-sm" role="alert" style="border: none; background: linear-gradient(135deg, #dc3545 0%, #c82333 100%); color: #fff;">
    <div class="d-flex align-items-center w-100">
        <div class="flex-shrink-0 me-3">
            <i class="material-symbols-outlined" style="font-size: 28px;">warning</i>
        </div>
        <div class="flex-grow-1">
            <strong>Important Notice: Service Shutdown</strong>
            <p class="mb-0 mt-1" style="font-size: 14px; opacity: 0.95;">
                Budgetku will be permanently discontinued on <strong>March 1, 2026</strong>. Please download your data before that date. After this date, all accounts and data will be permanently deleted and no longer accessible.
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
</div>
