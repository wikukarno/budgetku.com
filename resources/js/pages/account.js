(() => {
  const axiosCfg = { headers: { 'X-Requested-With': 'XMLHttpRequest' } };

  function q(id) { return document.getElementById(id); }
  function byName(name) { return document.querySelector(`[name="${name}"]`); }
  function setBtnLoading(btn, text) {
    if (!btn) return;
    if (!btn.dataset) btn.dataset = {};
    if (!btn.dataset.origHtml) btn.dataset.origHtml = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = `<span class="spinner-border spinner-border-sm me-1"></span>${text || 'Loading...'}`;
  }
  function clearBtnLoading(btn) {
    if (!btn) return;
    btn.disabled = false;
    if (btn.dataset && btn.dataset.origHtml !== undefined) {
      btn.innerHTML = btn.dataset.origHtml;
      delete btn.dataset.origHtml;
    }
  }

  // Profile update
  const accountForm = q('account-form');
  if (accountForm) {
    accountForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      try {
        const submitBtn = accountForm.querySelector('button[type="submit"]');
        setBtnLoading(submitBtn, 'Saving...');
        const fd = new FormData(accountForm);
        const url = accountForm.action;
        fd.append('_method', 'PUT');
        const res = await axios.post(url, fd, axiosCfg);
        if (res.data?.status) {
          window.showCustomAlert?.('success', res.data.message || 'Profile updated');
        } else {
          window.showCustomAlert?.('danger', res.data?.message || 'Failed to update profile');
        }
      } catch (err) {
        console.error(err);
        window.showCustomAlert?.('danger', 'Something went wrong!');
      } finally {
        const submitBtn = accountForm.querySelector('button[type="submit"]');
        clearBtnLoading(submitBtn);
      }
    });
  }

  // Change password + E2EE wrap rotate
  async function e2eeRotateAfterPasswordChange(curPass, newPass) {
    try {
      const keysRes = await axios.get('/e2ee/keys');
      const keys = keysRes.data || {};
      if (!keys.e2ee_enabled) return;
      let Rb64 = null;
      try {
        async function pollGetR(timeoutMs = 1000) {
          const start = Date.now();
          while (Date.now() - start < timeoutMs) {
            let r = window.E2EESession?.getR?.() || (window.E2EE_MEMORY_ONLY ? null : (()=>{ try { return sessionStorage.getItem('e2ee_R_b64'); } catch { return null; }})());
            if (r) return r;
            try { const mod = await import('../crypto/key-worker-client'); r = await mod.getR(); } catch { r = null; }
            if (r) return r;
            await new Promise(res => setTimeout(res, 100));
          }
          return null;
        }
        Rb64 = window.E2EESession?.getR?.() || (window.E2EE_MEMORY_ONLY ? null : (()=>{ try { return sessionStorage.getItem('e2ee_R_b64'); } catch { return null; }})());
        if (!Rb64) Rb64 = await pollGetR(1000);
      } catch {}
      if (!Rb64) {
        const wrapBytes = (b64) => Uint8Array.from(atob(b64), c => c.charCodeAt(0));
        const salt = wrapBytes(keys.e2ee_pass_salt);
        let pdk;
        if (keys.e2ee_kdf_params?.kdf === 'argon2id' && window.argon2) {
          const p = keys.e2ee_kdf_params || {};
          const r = await window.argon2.hash({ pass: curPass, salt, type: window.argon2.ArgonType.Argon2id, mem: p.mem || 65536, time: p.time || 3, parallelism: p.parallelism || 1, hashLen: 32, raw: true });
          pdk = await crypto.subtle.importKey('raw', r.hash, { name: 'AES-GCM' }, false, ['decrypt']);
        } else {
          const baseKey = await crypto.subtle.importKey('raw', new TextEncoder().encode(curPass), 'PBKDF2', false, ['deriveKey']);
          const iter = Math.max(310000, (keys.e2ee_kdf_params?.iter || 0));
          pdk = await crypto.subtle.deriveKey({ name: 'PBKDF2', salt, iterations: iter, hash: 'SHA-256' }, baseKey, { name: 'AES-GCM', length: 256 }, false, ['decrypt']);
        }
        const all = wrapBytes(keys.e2ee_pass_wrap);
        const iv = all.slice(0,12), ct = all.slice(12);
        const Rbuf = await crypto.subtle.decrypt({ name: 'AES-GCM', iv }, pdk, ct);
        Rb64 = btoa(String.fromCharCode(...new Uint8Array(Rbuf)));
        try { window.E2EESession?.setR?.(Rb64); } catch {}
      }
      const Rbytes = Uint8Array.from(atob(Rb64), c => c.charCodeAt(0));
      const newSaltBytes = crypto.getRandomValues(new Uint8Array(16));
      const newSalt = btoa(String.fromCharCode(...newSaltBytes));
      let newPdk;
      if (keys.e2ee_kdf_params?.kdf === 'argon2id' && window.argon2) {
        const p = keys.e2ee_kdf_params || {};
        const r2 = await window.argon2.hash({ pass: newPass, salt: newSaltBytes, type: window.argon2.ArgonType.Argon2id, mem: p.mem || 65536, time: p.time || 3, parallelism: p.parallelism || 1, hashLen: 32, raw: true });
        newPdk = await crypto.subtle.importKey('raw', r2.hash, { name: 'AES-GCM' }, false, ['encrypt']);
      } else {
        const base2 = await crypto.subtle.importKey('raw', new TextEncoder().encode(newPass), 'PBKDF2', false, ['deriveKey']);
        const iter = Math.max(310000, (keys.e2ee_kdf_params?.iter || 0));
        newPdk = await crypto.subtle.deriveKey({ name: 'PBKDF2', salt: newSaltBytes, iterations: iter, hash: 'SHA-256' }, base2, { name: 'AES-GCM', length: 256 }, false, ['encrypt']);
      }
      const iv2 = crypto.getRandomValues(new Uint8Array(12));
      const ct2 = await crypto.subtle.encrypt({ name: 'AES-GCM', iv: iv2 }, newPdk, Rbytes);
      const out = new Uint8Array(iv2.byteLength + ct2.byteLength);
      out.set(iv2, 0); out.set(new Uint8Array(ct2), iv2.byteLength);
      const passWrap = btoa(String.fromCharCode(...out));
      await axios.post('/e2ee/passphrase/rotate', {
        e2ee_pass_wrap: passWrap,
        e2ee_pass_salt: newSalt,
        e2ee_kdf_params: keys.e2ee_kdf_params || (window.argon2 ? { kdf: 'argon2id', mem: 65536, time: 3, parallelism: 1 } : { kdf: 'pbkdf2', iter: 310000, hash: 'SHA-256' }),
      });
      window.showCustomAlert?.('success', 'Encryption key updated for new password');
    } catch (err) { console.error('E2EE rotate error', err); }
  }
  const pwForm = q('change-password-form');
  if (pwForm) {
    pwForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      const fd = new FormData(pwForm);
      const url = pwForm.action;
      try {
        const submitBtn = pwForm.querySelector('button[type="submit"]');
        setBtnLoading(submitBtn, 'Updating...');
        const res = await axios.post(url, fd, { ...axiosCfg, params: { _method: 'PUT' } });
        if (res.data?.status) {
          window.showCustomAlert?.('success', res.data.message || 'Password updated');
          pwForm.reset();
          try { await e2eeRotateAfterPasswordChange(fd.get('current_password'), fd.get('new_password')); } catch (err) {}
        } else {
          window.showCustomAlert?.('danger', res.data?.message || 'Failed to update password');
        }
      } catch (err) {
        window.showCustomAlert?.('danger', err?.response?.data?.message || 'Something went wrong!');
      } finally {
        const submitBtn = pwForm.querySelector('button[type="submit"]');
        clearBtnLoading(submitBtn);
      }
    });
  }

  // Expose E2EE helpers present in inline (generate recovery, info, upgrade KDF)
  async function generateRecoveryAndDownload() {
    try {
      const { data: keys } = await axios.get('/e2ee/keys');
      if (!keys || !keys.e2ee_enabled) { window.showCustomAlert?.('danger', 'Please enable encryption first'); return; }
      let Rb64 = null; try {
        async function pollGetR(timeoutMs = 1000) {
          const start = Date.now();
          while (Date.now() - start < timeoutMs) {
            let r = window.E2EESession?.getR?.() || (window.E2EE_MEMORY_ONLY ? null : (()=>{ try { return sessionStorage.getItem('e2ee_R_b64'); } catch { return null; }})());
            if (r) return r;
            try { const mod = await import('../crypto/key-worker-client'); r = await mod.getR(); } catch { r = null; }
            if (r) return r;
            await new Promise(res => setTimeout(res, 100));
          }
          return null;
        }
        Rb64 = window.E2EESession?.getR?.() || (window.E2EE_MEMORY_ONLY ? null : (()=>{ try { return sessionStorage.getItem('e2ee_R_b64'); } catch { return null; }})());
        if (!Rb64) Rb64 = await pollGetR(1000);
      } catch {}
      if (!Rb64) {
        const { value: pass } = await Swal.fire({ title:'Unlock Encryption', input:'password', inputLabel:'Enter your passphrase', showCancelButton:true, confirmButtonText:'Unlock' });
        if (!pass) { window.showCustomAlert?.('danger','Cancelled'); return; }
        const wrapBytes = (b64) => Uint8Array.from(atob(b64), c => c.charCodeAt(0));
        const salt = wrapBytes(keys.e2ee_pass_salt);
        let pdk;
        if (keys.e2ee_kdf_params?.kdf === 'argon2id' && window.argon2) {
          const p = keys.e2ee_kdf_params || {};
          const r = await window.argon2.hash({ pass, salt, type: window.argon2.ArgonType.Argon2id, mem: p.mem || 65536, time: p.time || 3, parallelism: p.parallelism || 1, hashLen: 32, raw: true });
          pdk = await crypto.subtle.importKey('raw', r.hash, { name:'AES-GCM' }, false, ['decrypt']);
        } else {
          const baseKey = await crypto.subtle.importKey('raw', new TextEncoder().encode(pass), 'PBKDF2', false, ['deriveKey']);
          const iter = Math.max(310000, (keys.e2ee_kdf_params?.iter || 0));
          pdk = await crypto.subtle.deriveKey({ name:'PBKDF2', salt, iterations: iter, hash:'SHA-256' }, baseKey, { name:'AES-GCM', length:256 }, false, ['decrypt']);
        }
        const all = wrapBytes(keys.e2ee_pass_wrap);
        const iv = all.slice(0,12), ct = all.slice(12);
        const Rbuf = await crypto.subtle.decrypt({ name:'AES-GCM', iv }, pdk, ct);
        Rb64 = btoa(String.fromCharCode(...new Uint8Array(Rbuf)));
        try { window.E2EESession?.setR?.(Rb64); } catch {}
      }
      const recovery = (() => { const alphabet='ABCDEFGHJKLMNPQRSTUVWXYZ23456789'; const bytes=new Uint8Array(26); crypto.getRandomValues(bytes); let out=''; for (let i=0;i<bytes.length;i++) out += alphabet[bytes[i]%alphabet.length]; return out.match(/.{1,4}/g).join('-'); })();
      const recSaltBytes = new Uint8Array(16); crypto.getRandomValues(recSaltBytes);
      const recSalt = btoa(String.fromCharCode(...recSaltBytes));
      let rk;
      if (keys.e2ee_kdf_params?.kdf === 'argon2id' && window.argon2) {
        const p = keys.e2ee_kdf_params || {};
        const r3 = await window.argon2.hash({ pass: recovery, salt: recSaltBytes, type: window.argon2.ArgonType.Argon2id, mem: p.mem || 65536, time: p.time || 3, parallelism: p.parallelism || 1, hashLen: 32, raw: true });
        rk = await crypto.subtle.importKey('raw', r3.hash, { name:'AES-GCM' }, false, ['encrypt']);
      } else {
        const base = await crypto.subtle.importKey('raw', new TextEncoder().encode(recovery), 'PBKDF2', false, ['deriveKey']);
        const iter = Math.max(310000, (keys.e2ee_kdf_params?.iter || 0));
        rk = await crypto.subtle.deriveKey({ name:'PBKDF2', salt: recSaltBytes, iterations: iter, hash:'SHA-256' }, base, { name:'AES-GCM', length:256 }, false, ['encrypt']);
      }
      const Rbytes = Uint8Array.from(atob(Rb64), c => c.charCodeAt(0));
      const iv2 = new Uint8Array(12); crypto.getRandomValues(iv2);
      const ct2 = await crypto.subtle.encrypt({ name:'AES-GCM', iv: iv2 }, rk, Rbytes);
      const out = new Uint8Array(iv2.byteLength + ct2.byteLength); out.set(iv2,0); out.set(new Uint8Array(ct2), iv2.byteLength);
      const recWrap = btoa(String.fromCharCode(...out));
      await axios.post('/e2ee/recovery/rotate', { e2ee_rec_wrap: recWrap, e2ee_rec_salt: recSalt });
      const lines = ['BudgetKu Recovery Code','Date: '+new Date().toISOString(),'User: '+(document.querySelector('input[name=email]')?.value||''),'','RECOVERY_CODE='+recovery,'','Keep this file safe. Anyone with this code can recover your encryption keys.'];
      const blob = new Blob([lines.join('\n')], { type:'text/plain;charset=utf-8' });
      const a = document.createElement('a'); a.href = URL.createObjectURL(blob); a.download='BudgetKu-Recovery-'+Date.now()+'.txt'; a.click(); URL.revokeObjectURL(a.href);
      window.showCustomAlert?.('success','New recovery generated and downloaded');
    } catch (err) { console.error(err); window.showCustomAlert?.('danger','Failed to generate recovery'); }
  }
  function showCurrentRecoveryInfo() { 
    const modal = new window.bootstrap.Modal(document.getElementById('recoveryInfoModal'));
    modal.show();
  }


  // Expose to window for onclick compatibility
  window.generateRecoveryAndDownload = generateRecoveryAndDownload;
  window.showCurrentRecoveryInfo = showCurrentRecoveryInfo;
  // Two-Factor Authentication
  async function showModalTwoFA() {
    const qrImg = document.getElementById('qr-code-img');
    const secretEl = document.getElementById('secret-key');
    const otpInput = document.getElementById('otp-code');
    const list = document.getElementById('recovery-codes');
    if (qrImg) qrImg.src = '';
    if (secretEl) secretEl.textContent = '';
    if (otpInput) otpInput.value = '';
    if (list) list.innerHTML = '';
    document.getElementById('recovery-section')?.setAttribute('hidden', 'true');
    document.getElementById('otp-section')?.setAttribute('hidden', 'true');
    document.getElementById('qr-section')?.setAttribute('hidden', 'true');
    $('#enableTwoFactorLabel').text('Enable Two-Factor Authentication');
    $('#enableTwoFactor').modal('show');

    const form = document.getElementById('enable-2fa-form');
    const fd = new FormData(form);
    await axios.post('/2fa/setup', fd, { headers: { 'X-CSRF-TOKEN': fd.get('_token'), 'X-Requested-With': 'XMLHttpRequest' } })
      .then(res => {
        window.showCustomAlert?.('success', 'QR Code generated. Please scan and verify!');
        if (qrImg) qrImg.src = res.data.qr_code;
        if (secretEl) secretEl.textContent = res.data.secret;
        document.getElementById('enable-2fa-section')?.setAttribute('hidden', 'true');
        document.getElementById('qr-section')?.removeAttribute('hidden');
        document.getElementById('otp-section')?.setAttribute('hidden', 'true');
        document.getElementById('btnVerify2FA')?.setAttribute('hidden', 'true');
        document.getElementById('btnNext2FA')?.removeAttribute('hidden');
      })
      .catch(() => window.showCustomAlert?.('danger', 'Failed to enable 2FA'));
  }
  function btnNextOtp() { document.getElementById('qr-section')?.setAttribute('hidden','true'); document.getElementById('otp-section')?.removeAttribute('hidden'); document.getElementById('btnVerify2FA')?.removeAttribute('hidden'); document.getElementById('btnNext2FA')?.setAttribute('hidden','true'); }
  async function btnVerifyOtp() {
    const code = (document.getElementById('otp-code')?.value || '').trim();
    const form = document.getElementById('enable-2fa-form');
    const fd = new FormData(form); fd.append('code', code);
    if (code.length !== 6) { window.showCustomAlert?.('danger','OTP code must be 6 digits'); return; }
    await axios.post('/2fa/verify', fd, { headers: { 'X-CSRF-TOKEN': fd.get('_token'), 'X-Requested-With': 'XMLHttpRequest' } })
      .then(res => {
        window.showCustomAlert?.('success','Two-Factor Authentication enabled!');
        document.getElementById('btnNext2FA')?.setAttribute('hidden','true');
        document.getElementById('btnVerify2FA')?.setAttribute('hidden','true');
        document.getElementById('recovery-section')?.removeAttribute('hidden');
        const list = document.getElementById('recovery-codes'); if (list) { list.innerHTML=''; (res.data.recovery_codes||[]).forEach(code=>{ const li=document.createElement('li'); li.className='list-group-item'; li.textContent=code; list.appendChild(li); }); }
        document.getElementById('btn-enable-2fa')?.setAttribute('hidden','true');
        const disableSection = document.getElementById('disable-2fa-section'); if (disableSection) disableSection.removeAttribute('hidden');
      })
      .catch(err => window.showCustomAlert?.('danger', err?.response?.data?.message || 'Invalid OTP code!'));
  }
  function cancelTwoFA() { document.getElementById('enable-2fa-section')?.removeAttribute('hidden'); document.getElementById('qr-section')?.setAttribute('hidden','true'); document.getElementById('otp-section')?.setAttribute('hidden','true'); const qrImg=document.getElementById('qr-code-img'); if (qrImg) qrImg.src=''; const secretEl=document.getElementById('secret-key'); if (secretEl) secretEl.textContent=''; const otpInput=document.getElementById('otp-code'); if (otpInput) otpInput.value=''; $('#enableTwoFactor').modal('hide'); }
  async function downloadRecoveryCodes() { const listItems=document.querySelectorAll('#recovery-codes li'); if (!listItems.length) { window.showCustomAlert?.('danger','No recovery codes to download'); return; } const codes=Array.from(listItems).map(li=>li.textContent); const blob=new Blob([codes.join('\n')],{type:'text/plain'}); const link=document.createElement('a'); link.href=URL.createObjectURL(blob); link.download='recovery-codes.txt'; document.body.appendChild(link); link.click(); document.body.removeChild(link); window.showCustomAlert?.('success','Recovery codes downloaded.'); await axios.post('/2fa/mark-downloaded', null, axiosCfg).catch(()=>{}); }
  function bindDisableForm(){ const form=document.getElementById('disable-2fa-form'); if (!form) return; form.addEventListener('submit', function(e){ e.preventDefault(); Swal.fire({ title:'Are you sure?', text:'This will disable 2FA on your account.', icon:'warning', showCancelButton:true, confirmButtonColor:'#d33', cancelButtonColor:'#6c757d', confirmButtonText:'Yes, disable it' }).then(async (r)=>{ if (!r.isConfirmed) return; const fd=new FormData(form); await axios.post('/2fa/disable', fd, { headers:{ 'X-CSRF-TOKEN': fd.get('_token'), 'X-Requested-With':'XMLHttpRequest' } }).then(()=>{ window.showCustomAlert?.('success','Two-Factor Authentication disabled.'); document.getElementById('disable-2fa-section')?.setAttribute('hidden','true'); document.getElementById('btn-enable-2fa')?.removeAttribute('hidden'); }).catch(()=> window.showCustomAlert?.('danger','Failed to disable 2FA')); }); }); }
  bindDisableForm();
  // Bind buttons (no inline handlers)
  document.getElementById('btn-enable-2fa')?.addEventListener('click', showModalTwoFA);
  document.getElementById('btn-download-recovery')?.addEventListener('click', generateRecoveryAndDownload);
  document.getElementById('btn-recovery-info')?.addEventListener('click', showCurrentRecoveryInfo);
  document.getElementById('btn-download-2fa-codes')?.addEventListener('click', downloadRecoveryCodes);
  document.getElementById('btn-cancel-2fa')?.addEventListener('click', cancelTwoFA);
  document.getElementById('btnNext2FA')?.addEventListener('click', btnNextOtp);
  document.getElementById('btnVerify2FA')?.addEventListener('click', btnVerifyOtp);
  // Tab hash handling for CSP-safe behavior (no inline JS)
  document.addEventListener('DOMContentLoaded', function () {
    const hash = window.location.hash;
    if (hash) {
      const tabTrigger = document.querySelector(`button[data-bs-target="${hash}"]`);
      if (tabTrigger && window.bootstrap?.Tab) {
        new bootstrap.Tab(tabTrigger).show();
      }
    }
    const tabButtons = document.querySelectorAll('#settingsTab button[data-bs-toggle="tab"]');
    tabButtons.forEach(button => {
      button.addEventListener('shown.bs.tab', function (e) {
        const target = e.target.getAttribute('data-bs-target');
        history.replaceState(null, null, target);
      });
    });
  });
  const delForm = q('delete-account-form'); if (delForm) { delForm.addEventListener('submit', function(e){ e.preventDefault(); Swal.fire({ title:'Are you sure?', text:'Your account will be permanently deleted!', icon:'warning', showCancelButton:true, confirmButtonColor:'#d33', cancelButtonColor:'#6c757d', confirmButtonText:'Yes, delete it!' }).then(async (r)=>{ if (!r.isConfirmed) return; try { const res= await axios.post(delForm.action, null, { ...axiosCfg, params: { _method:'DELETE' } }); if (res.data?.status) { Swal.fire({ icon:'success', title:'Deleted!', text: res.data.message, timer:2000, showConfirmButton:false }).then(()=> window.location.href='/login'); } else { Swal.fire('Error', res.data?.message || 'Failed to delete account', 'error'); } } catch { Swal.fire('Error', 'Something went wrong!', 'error'); } }); }); }
  window.showModalTwoFA = showModalTwoFA;
  window.btnNextOtp = btnNextOtp;
  window.btnVerifyOtp = btnVerifyOtp;
  window.cancelTwoFA = cancelTwoFA;
  window.downloadRecoveryCodes = downloadRecoveryCodes;
  window.bindDisableForm = bindDisableForm;
  window.e2eeRotateAfterPasswordChange = e2eeRotateAfterPasswordChange;
})();
