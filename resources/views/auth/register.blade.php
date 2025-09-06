@extends('layouts.auth')

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-md-8 col-lg-6">
        <div class="mx-auto">
            <div class="mb-3 text-center d-flex justify-content-center">
                <img src="{{ asset('v2/images/logo.svg') }}" class="rounded-3 for-light-logo d-block mx-auto" alt="register">
                {{-- <img src="{{ asset('v2/images/logo.svg') }}" class="rounded-3 for-dark-logo d-block mx-auto" alt="register"> --}}
            </div>
            <h3 class="fs-22 mb-1 text-center">Create your BudgetKu account</h3>
            <p class="text-muted fs-13 mb-4 text-center">Quick and secure. Your data stays private.</p>

            <!-- Google button moved below the form -->

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                        <li class="mb-1">{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form id="register-form" action="{{ route('register') }}" method="POST" novalidate>
                @csrf
                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <div class="form-group mb-0">
                            <label class="label text-secondary fs-12">Full Name</label>
                            <div class="position-relative">
                                <input type="text" name="name" class="form-control text-dark ps-5 h-55" placeholder="Your name" value="{{ old('name') }}" required>
                                <i class="ri-user-line position-absolute top-50 start-0 translate-middle-y fs-20 text-gray-light ps-20"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="form-group mb-0">
                            <label class="label text-secondary fs-12">Email Address</label>
                            <div class="position-relative">
                                <input type="email" name="email" class="form-control text-dark ps-5 h-55" placeholder="example@budgetku.com" value="{{ old('email') }}" required>
                                <i class="ri-mail-line position-absolute top-50 start-0 translate-middle-y fs-20 text-gray-light ps-20"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="form-group mb-0">
                            <label class="label text-secondary fs-12">Password</label>
                            <div class="position-relative">
                                <input type="password" name="password" id="reg-password" class="form-control text-dark ps-5 pe-5 h-55" placeholder="Create a strong password" required>
                                <i class="ri-lock-2-line position-absolute top-50 start-0 translate-middle-y fs-20 text-gray-light ps-20"></i>
                                <button type="button" class="btn bg-transparent border-0 position-absolute top-50 end-0 translate-middle-y me-2 p-0 text-secondary text-decoration-none" tabindex="-1" onclick="togglePw('reg-password', this)"><i class="ri-eye-line"></i></button>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="form-group mb-0">
                            <label class="label text-secondary fs-12">Confirm Password</label>
                            <div class="position-relative">
                                <input type="password" name="password_confirmation" id="reg-password2" class="form-control text-dark ps-5 pe-5 h-55" placeholder="Re-enter password" required>
                                <i class="ri-lock-password-line position-absolute top-50 start-0 translate-middle-y fs-20 text-gray-light ps-20"></i>
                                <button type="button" class="btn bg-transparent border-0 position-absolute top-50 end-0 translate-middle-y me-2 p-0 text-secondary text-decoration-none" tabindex="-1" onclick="togglePw('reg-password2', this)"><i class="ri-eye-line"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-4"></div>
                <div class="form-group mb-4">
                    <button type="submit" class="btn btn-primary fw-medium py-2 px-3 w-100">
                        <div class="d-flex align-items-center justify-content-center py-1">
                            <i class="material-symbols-outlined text-white fs-20 me-2">person_add</i>
                            <span>Create Account</span>
                        </div>
                    </button>
                    <div class="d-flex justify-content-center mt-2">
                        <div class="d-inline-flex align-items-center text-success">
                            <i class="ri-shield-keyhole-line me-2"></i>
                            <small class="fw-medium">End-to-end encrypted</small>
                        </div>
                    </div>
                    <div class="d-flex align-items-center my-3">
                        <hr class="flex-grow-1 m-0">
                        <span class="px-2 text-muted small">or sign up with</span>
                        <hr class="flex-grow-1 m-0">
                    </div>
                    <div class="d-flex justify-content-center">
                        <a href="{{ url('auth/redirect') }}" class="btn btn-outline-secondary bg-transparent w-100 py-2 hover-bg" style="border-color:#D6DAE1; max-width:260px;">
                            <img src="{{ asset('v2/images/google.svg') }}" alt="google">
                        </a>
                    </div>
                </div>
            </form>
            <div class="text-center mt-2">
                <span class="text-muted fs-12">Already have an account?</span>
                <a href="{{ route('login') }}" class="fw-medium">Login</a>
            </div>
        </div>
    </div>
</div>
@endsection
@push('after-scripts')
<script>
  (function(){
    const form = document.getElementById('register-form');
    if (!form) return;
    form.addEventListener('submit', async function(e){
      e.preventDefault();
      const fd = new FormData(form);
      const pass = form.querySelector('input[name="password"]').value;
      // clear previous errors
      form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
      form.querySelectorAll('.field-error').forEach(el => el.remove());
      // Client-side validation
      let valid = true;
      const nameInput = form.querySelector('[name="name"]');
      const emailInput = form.querySelector('[name="email"]');
      const passInput = form.querySelector('[name="password"]');
      const pass2Input = form.querySelector('[name="password_confirmation"]');
      const emailRe = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!nameInput.value.trim() || nameInput.value.trim().length < 3) { setFieldError(nameInput, 'Name must be at least 3 characters'); valid = false; }
      if (!emailRe.test(emailInput.value.trim())) { setFieldError(emailInput, 'Please enter a valid email'); valid = false; }
      if (!passInput.value || passInput.value.length < 8) { setFieldError(passInput, 'Password must be at least 8 characters'); valid = false; }
      if (passInput.value !== pass2Input.value) { setFieldError(pass2Input, 'Passwords do not match'); valid = false; }
      if (!valid) return;
      // Disable button
      const btn = form.querySelector('button[type="submit"]');
      const originalBtn = btn.innerHTML; btn.disabled = true; btn.innerHTML = '<div class="d-flex align-items-center justify-content-center py-1"><i class="material-symbols-outlined text-white fs-20 me-2">hourglass_top</i><span>Processing...</span></div>';
      try {
        const res = await axios.post(form.action, fd, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }});
        await autoSetupE2EE(pass);
        window.location.href = (res.data && res.data.redirect) ? res.data.redirect : '/';
      } catch (err) {
        const resp = err.response;
        if (resp && resp.status === 422 && resp.data && resp.data.errors) {
          const errors = resp.data.errors;
          for (const field in errors) {
            const input = form.querySelector(`[name="${field}"]`);
            if (input) {
              setFieldError(input, errors[field][0]);
            }
          }
        } else {
          showCustomAlert?.('danger', 'Registration failed');
        }
      }
      finally { btn.disabled = false; btn.innerHTML = originalBtn; }
    });

    async function autoSetupE2EE(pass) {
      const enc = (s) => new TextEncoder().encode(s);
      const b64 = (bytes) => btoa(String.fromCharCode(...new Uint8Array(bytes)));
      const rand = (n) => { const a=new Uint8Array(n); crypto.getRandomValues(a); return a; };
      const deriveArgon2id = async (secret, saltBytes, params) => {
        const r = await window.argon2.hash({ pass: secret, salt: saltBytes, type: window.argon2.ArgonType.Argon2id, mem: params.mem, time: params.time, parallelism: params.parallelism, hashLen: 32, raw: true });
        return crypto.subtle.importKey('raw', r.hash, { name: 'AES-GCM' }, false, ['encrypt','decrypt']);
      };
      const derivePBKDF2 = async (secret, salt) => {
        const base = await crypto.subtle.importKey('raw', enc(secret), 'PBKDF2', false, ['deriveKey']);
        return crypto.subtle.deriveKey({ name:'PBKDF2', salt, iterations:310000, hash:'SHA-256' }, base, { name:'AES-GCM', length:256 }, false, ['encrypt','decrypt']);
      };
      const aesEnc = async (key, dataBytes) => {
        const iv = rand(12);
        const ct = await crypto.subtle.encrypt({ name:'AES-GCM', iv }, key, dataBytes);
        const out = new Uint8Array(iv.byteLength + ct.byteLength);
        out.set(iv, 0); out.set(new Uint8Array(ct), iv.byteLength);
        return b64(out);
      };

      const R = rand(32); const Rb64 = b64(R);
      const alphabet = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
      const rbytes = rand(26); let recovery = ''; for (let i=0;i<rbytes.length;i++) recovery += alphabet[rbytes[i]%alphabet.length];
      recovery = recovery.match(/.{1,4}/g).join('-');

      const { privateKey, publicKey } = await openpgp.generateKey({ type:'ecc', curve:'curve25519', userIDs:[{ name:'{{ addslashes(Auth::user()->name ?? "User") }}', email:'{{ addslashes(Auth::user()->email ?? "user@example.com") }}' }], passphrase: Rb64 });

      const passSaltBytes = rand(16); const passSalt = b64(passSaltBytes);
      const recSaltBytes = rand(16); const recSalt = b64(recSaltBytes);
      const kdfParams = window.argon2 ? { kdf:'argon2id', mem:65536, time:3, parallelism:1 } : { kdf:'pbkdf2', iter:310000, hash:'SHA-256' };
      const pdk = window.argon2 ? await deriveArgon2id(pass, passSaltBytes, kdfParams) : await derivePBKDF2(pass, passSaltBytes);
      const rk  = window.argon2 ? await deriveArgon2id(recovery, recSaltBytes, kdfParams) : await derivePBKDF2(recovery, recSaltBytes);
      const passWrap = await aesEnc(pdk, R);
      const recWrap = await aesEnc(rk, R);

      await axios.post('{{ url('/e2ee/keys') }}', {
        pgp_public_key: publicKey,
        pgp_private_key_armor: privateKey,
        e2ee_pass_wrap: passWrap,
        e2ee_pass_salt: passSalt,
        e2ee_rec_wrap: recWrap,
        e2ee_rec_salt: recSalt,
        e2ee_kdf_params: kdfParams,
      });

      try { window.E2EESession?.setR?.(Rb64, { persist: true }); } catch (e) { try { sessionStorage.setItem('e2ee_R_b64', Rb64); } catch (e2) {} }
    }
  })();
  function togglePw(id, btn){
    const input = document.getElementById(id);
    if (!input) return;
    const is = input.getAttribute('type') === 'password';
    input.setAttribute('type', is ? 'text' : 'password');
    const icon = btn.querySelector('i');
    if (icon) icon.className = is ? 'ri-eye-off-line' : 'ri-eye-line';
  }
  function setFieldError(inputEl, message) {
    inputEl.classList.add('is-invalid');
    const div = document.createElement('div');
    div.className = 'text-danger small mt-1 field-error';
    div.innerText = message;
    inputEl.parentNode.appendChild(div);
  }
</script>
@endpush
