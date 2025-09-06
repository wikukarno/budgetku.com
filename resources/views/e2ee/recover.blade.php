@extends('layouts.v2.app')

@section('title', 'Recover E2EE Access')

@section('content')
<div class="card bg-white border-0 rounded-3 mb-4">
  <div class="card-body p-4">
    <h3 class="mb-3">Recover Encrypted Access</h3>
    <p class="text-muted mb-4">Use your Recovery Code to set a new passphrase. Your data stays encrypted; we only re-wrap your key, no re‑encryption of content needed.</p>

    <div class="mb-3">
      <label class="form-label">Recovery Code</label>
      <input type="text" class="form-control" id="recovery-code" placeholder="XXXX-XXXX-XXXX-XXXX-..." />
    </div>

    <div class="row">
      <div class="col-md-6 mb-3">
        <label class="form-label">New Passphrase</label>
        <input type="password" class="form-control" id="new-pass" placeholder="Enter a strong passphrase" />
      </div>
      <div class="col-md-6 mb-4">
        <label class="form-label">Confirm New Passphrase</label>
        <input type="password" class="form-control" id="new-pass2" placeholder="Re-enter passphrase" />
      </div>
    </div>

    <div class="d-flex gap-2">
      <button id="btn-recover" class="btn btn-primary">Recover & Update</button>
      <a href="{{ route('home') }}" class="btn btn-light">Cancel</a>
    </div>

    <div id="status" class="mt-3 text-muted"></div>
  </div>
</div>
@endsection

@push('after-scripts')
<script>
  axios.defaults.headers.common['X-CSRF-TOKEN'] = '{{ csrf_token() }}';
  const enc = (s) => new TextEncoder().encode(s);

  function b64(bytes) {
    return btoa(String.fromCharCode(...new Uint8Array(bytes)));
  }
  function b64toBytes(b64str) {
    const bin = atob(b64str);
    const bytes = new Uint8Array(bin.length);
    for (let i = 0; i < bin.length; i++) bytes[i] = bin.charCodeAt(i);
    return bytes;
  }
  function randomBytes(n) {
    const a = new Uint8Array(n);
    crypto.getRandomValues(a);
    return a;
  }
  async function deriveAesKey(pass, saltB64, params) {
    const salt = typeof saltB64 === 'string' ? b64toBytes(saltB64) : saltB64;
    if (params?.kdf === 'argon2id' && window.argon2) {
      const res = await window.argon2.hash({ pass, salt, type: window.argon2.ArgonType.Argon2id, mem: params.mem || 65536, time: params.time || 3, parallelism: params.parallelism || 1, hashLen: 32, raw: true });
      return crypto.subtle.importKey('raw', res.hash, { name: 'AES-GCM' }, false, ['encrypt','decrypt']);
    }
    const iter = Math.max(310000, (params?.iter || 0));
    const baseKey = await crypto.subtle.importKey('raw', enc(pass), 'PBKDF2', false, ['deriveKey']);
    return crypto.subtle.deriveKey({ name: 'PBKDF2', salt, iterations: iter, hash: 'SHA-256' }, baseKey, { name: 'AES-GCM', length: 256 }, false, ['encrypt','decrypt']);
  }
  async function aesGcmDecrypt(key, b64IvCt) {
    const all = b64toBytes(b64IvCt);
    const iv = all.slice(0,12); const ct = all.slice(12);
    const plain = await crypto.subtle.decrypt({ name: 'AES-GCM', iv }, key, ct);
    return new Uint8Array(plain);
  }
  async function aesGcmEncrypt(key, dataBytes) {
    const iv = randomBytes(12);
    const ct = await crypto.subtle.encrypt({ name: 'AES-GCM', iv }, key, dataBytes);
    const out = new Uint8Array(iv.byteLength + ct.byteLength);
    out.set(iv, 0); out.set(new Uint8Array(ct), iv.byteLength);
    return b64(out);
  }

  document.getElementById('btn-recover').addEventListener('click', async () => {
    const status = document.getElementById('status');
    status.textContent = '';
    const recovery = (document.getElementById('recovery-code').value || '').trim();
    const pass1 = document.getElementById('new-pass').value;
    const pass2 = document.getElementById('new-pass2').value;
    if (!recovery) { showCustomAlert?.('danger', 'Recovery code required'); return; }
    if (!pass1 || pass1 !== pass2) { showCustomAlert?.('danger', 'Passphrase mismatch'); return; }

    try {
      status.textContent = 'Fetching key envelope...';
      const { data: keys } = await axios.get('{{ url('/e2ee/keys') }}');
      if (!keys || !keys.e2ee_enabled) { showCustomAlert?.('danger', 'E2EE not enabled'); return; }

      // 1) Unwrap R using Recovery Code
      status.textContent = 'Validating recovery code...';
      const iter = Math.max(310000, (keys.e2ee_kdf_params?.iter || 0));
      const rk = await deriveAesKey(recovery, keys.e2ee_rec_salt, keys.e2ee_kdf_params);
      const Rbytes = await aesGcmDecrypt(rk, keys.e2ee_rec_wrap);
      const Rb64 = b64(Rbytes);

      // 2) Re-wrap with new passphrase
      status.textContent = 'Wrapping new passphrase...';
      const passSalt = b64(randomBytes(16));
      const pdk = await deriveAesKey(pass1, passSalt, keys.e2ee_kdf_params);
      const passWrap = await aesGcmEncrypt(pdk, Rbytes);

      // 3) Rotate on server
      status.textContent = 'Updating server...';
      await axios.post('{{ url('/e2ee/passphrase/rotate') }}', {
        e2ee_pass_wrap: passWrap,
        e2ee_pass_salt: passSalt,
        e2ee_kdf_params: keys.e2ee_kdf_params || (window.argon2 ? { kdf: 'argon2id', mem: 65536, time: 3, parallelism: 1 } : { kdf: 'pbkdf2', iter: 310000, hash: 'SHA-256' }),
      });

      try { window.E2EESession?.setR?.(Rb64, { persist: true }); } catch (e) {}
      showCustomAlert?.('success', 'Passphrase updated');
      setTimeout(() => window.location.href = '{{ route('home') }}', 800);
    } catch (err) {
      console.error(err);
      showCustomAlert?.('danger', 'Recovery failed. Check code and try again.');
      status.textContent = '';
    }
  });
</script>
@endpush
