@extends('layouts.v2.app')

@section('title', 'End-to-End Encryption Setup')

@section('content')
<div class="card bg-white border-0 rounded-3 mb-4">
  <div id="e2ee-setup-root" class="card-body p-4" data-user-name="{{ addslashes(auth()->user()->name ?? 'User') }}" data-user-email="{{ addslashes(auth()->user()->email ?? 'user@example.com') }}">
    <h3 class="mb-3">Enable End-to-End Encryption</h3>
    <p class="text-muted mb-4">Your encryption keys are generated and stored only on your device. Choose a strong passphrase. Password login changes won’t affect your encrypted data.</p>

    <div id="pass-inputs" class="mb-3">
      <label class="form-label">E2EE Passphrase</label>
      <input type="password" class="form-control" id="e2ee-pass" placeholder="Enter a strong passphrase" />
    </div>
    <div id="pass-confirm" class="mb-4">
      <label class="form-label">Confirm Passphrase</label>
      <input type="password" class="form-control" id="e2ee-pass2" placeholder="Re-enter passphrase" />
    </div>

    <div id="recovery-block" class="d-none mb-4">
      <label class="form-label">Recovery Code (save this securely)</label>
      <div class="input-group">
        <input type="text" class="form-control" id="recovery-code" readonly />
        <button class="btn btn-outline-secondary" type="button" id="copy-recovery">Copy</button>
      </div>
      <small class="text-muted">This code can recover your keys if you forget the passphrase. We cannot restore it.</small>
      <div class="form-check mt-2">
        <input class="form-check-input" type="checkbox" id="recovery-saved">
        <label class="form-check-label" for="recovery-saved">I have saved my recovery code</label>
      </div>
    </div>

    <div class="d-flex gap-2">
      <button id="btn-generate" class="btn btn-primary">Generate & Enable</button>
      <a href="{{ route('home') }}" class="btn btn-light">Cancel</a>
    </div>

    <div id="status" class="mt-3 text-muted"></div>
  </div>
</div>
@endsection

@push('after-scripts')
@endpush
