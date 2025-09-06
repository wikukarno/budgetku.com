(() => {
  const form = document.getElementById('login-form');
  if (!form) return;

  form.addEventListener('submit', async function(e){
    e.preventDefault();
    const fd = new FormData(form);
    const emailInput = form.querySelector('input[name="email"]');
    const passInput = form.querySelector('input[name="password"]');
    const pass = passInput.value;

    // Clear previous inline errors
    form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
    form.querySelectorAll('.field-error').forEach(el => el.remove());

    // Client-side validation
    let valid = true;
    const email = emailInput.value.trim();
    const emailRe = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!email || !emailRe.test(email)) { setFieldError(emailInput, 'Please enter a valid email'); valid = false; }
    if (!pass || pass.length < 8) { setFieldError(passInput, 'Password must be at least 8 characters'); valid = false; }
    if (!valid) return;

    const btn = form.querySelector('button[type="submit"]');
    const originalBtn = btn.innerHTML; btn.disabled = true;
    btn.innerHTML = '<div class="d-flex align-items-center justify-content-center py-1"><i class="material-symbols-outlined text-white fs-20 me-2">hourglass_top<\/i><span>Processing...<\/span><\/div>';
    try {
      const res = await axios.post(form.action, fd, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } });
      try {
        const keysRes = await axios.get('/e2ee/keys');
        const keys = keysRes.data || {};
        if (keys && keys.e2ee_enabled) {
          const wrapBytes = (b64) => Uint8Array.from(atob(b64), c => c.charCodeAt(0));
          const salt = wrapBytes(keys.e2ee_pass_salt);
          // Derive using Argon2id if configured and available; else PBKDF2
          let pdk;
          if (keys.e2ee_kdf_params?.kdf === 'argon2id' && window.argon2) {
            const p = keys.e2ee_kdf_params || {};
            const r = await window.argon2.hash({ pass, salt, type: window.argon2.ArgonType.Argon2id, mem: p.mem || 65536, time: p.time || 3, parallelism: p.parallelism || 1, hashLen: 32, raw: true });
            pdk = await crypto.subtle.importKey('raw', r.hash, { name: 'AES-GCM' }, false, ['decrypt']);
          } else {
            const baseKey = await crypto.subtle.importKey('raw', new TextEncoder().encode(pass), 'PBKDF2', false, ['deriveKey']);
            const iter = Math.max(310000, (keys.e2ee_kdf_params?.iter || 0));
            pdk = await crypto.subtle.deriveKey({ name:'PBKDF2', salt, iterations: iter, hash:'SHA-256' }, baseKey, { name:'AES-GCM', length:256 }, false, ['decrypt']);
          }
          const all = wrapBytes(keys.e2ee_pass_wrap);
          const iv = all.slice(0,12), ct = all.slice(12);
          const Rbuf = await crypto.subtle.decrypt({ name:'AES-GCM', iv }, pdk, ct);
          const Rb64 = btoa(String.fromCharCode(...new Uint8Array(Rbuf)));
          try {
            // Simpan untuk sesi browser (sessionStorage) agar tidak diminta ulang saat refresh
            window.E2EESession?.setR(Rb64, { persist: true, ttlMs: 30*24*60*60*1000 }); // 30 hari
            // Share juga ke SharedWorker agar survive antar tab selama sesi
            try { const mod = await import('../crypto/key-worker-client'); await mod.setR(Rb64, 30*24*60*60*1000); } catch {}
          } catch (e) {}
        }
      } catch (_) {}
      window.location.href = (res.data && res.data.redirect) ? res.data.redirect : '/';
    } catch (err) {
      const resp = err.response;
      if (resp && resp.status === 422) {
        const message = resp.data?.message || 'Invalid credentials';
        setFieldError(passInput, message);
      } else {
        window.showCustomAlert?.('danger', 'Login failed');
      }
    } finally {
      btn.disabled = false; btn.innerHTML = originalBtn;
    }

    function setFieldError(inputEl, message) {
      inputEl.classList.add('is-invalid');
      const div = document.createElement('div');
      div.className = 'text-danger small mt-1 field-error';
      div.innerText = message;
      inputEl.parentNode.appendChild(div);
    }
  });
})();
