// E2EE setup page logic moved from Blade inline script
(() => {
  document.addEventListener('DOMContentLoaded', () => {
    const root = document.getElementById('e2ee-setup-root');
    const btn = document.getElementById('btn-generate');
    if (!root || !btn) return;

    const enc = (s) => new TextEncoder().encode(s);
    const dec = (b) => new TextDecoder().decode(b);

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

    async function aesGcmEncrypt(key, dataBytes) {
      const iv = randomBytes(12);
      const ct = await crypto.subtle.encrypt({ name: 'AES-GCM', iv }, key, dataBytes);
      const out = new Uint8Array(iv.byteLength + ct.byteLength);
      out.set(iv, 0); out.set(new Uint8Array(ct), iv.byteLength);
      return b64(out);
    }

    function genRecoveryCode(len = 26) {
      const alphabet = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
      let out = '';
      const bytes = randomBytes(len);
      for (let i = 0; i < len; i++) out += alphabet[bytes[i] % alphabet.length];
      return out.match(/.{1,4}/g).join('-');
    }

    async function checkAlreadyEnabled() {
      try {
        const res = await axios.get('/e2ee/keys');
        if (res.data && res.data.e2ee_enabled) {
          window.location.href = '/';
        }
      } catch (e) { /* ignore */ }
    }

    const showCustomAlert = window.showCustomAlert || function(type, msg){ console.log(type, msg); };

    checkAlreadyEnabled();

    const copyBtn = document.getElementById('copy-recovery');
    if (copyBtn) {
      copyBtn.addEventListener('click', async () => {
        const v = document.getElementById('recovery-code').value;
        await navigator.clipboard.writeText(v);
        showCustomAlert('success', 'Recovery code copied');
      });
    }

    btn.addEventListener('click', async () => {
      const pass = document.getElementById('e2ee-pass').value;
      const pass2 = document.getElementById('e2ee-pass2').value;
      const status = document.getElementById('status');
      const userName = root.dataset.userName || 'User';
      const userEmail = root.dataset.userEmail || 'user@example.com';
      status.textContent = '';

      if (!pass || pass !== pass2) {
        showCustomAlert('danger', 'Passphrase mismatch');
        return;
      }

      try {
        const R = randomBytes(32);
        const Rb64 = b64(R);
        const recovery = genRecoveryCode();

        status.textContent = 'Generating keys...';
        const { privateKey, publicKey } = await window.openpgp.generateKey({
          type: 'ecc',
          curve: 'curve25519',
          userIDs: [{ name: userName, email: userEmail }],
          passphrase: Rb64,
        });

        status.textContent = 'Wrapping storage key...';
        const passSalt = b64(randomBytes(16));
        const recSalt = b64(randomBytes(16));
        const kdfParams = window.argon2 ? { kdf: 'argon2id', mem: 65536, time: 3, parallelism: 1 } : { kdf: 'pbkdf2', iter: 310000, hash: 'SHA-256' };
        const pdk = await deriveAesKey(pass, passSalt, kdfParams);
        const rk = await deriveAesKey(recovery, recSalt, kdfParams);
        const passWrap = await aesGcmEncrypt(pdk, R);
        const recWrap = await aesGcmEncrypt(rk, R);

        status.textContent = 'Saving to server...';
        await axios.post('/e2ee/keys', {
          pgp_public_key: publicKey,
          pgp_private_key_armor: privateKey,
          e2ee_pass_wrap: passWrap,
          e2ee_pass_salt: passSalt,
          e2ee_rec_wrap: recWrap,
          e2ee_rec_salt: recSalt,
          e2ee_kdf_params: kdfParams,
        });

        // Simpan R untuk sesi agar tidak minta passphrase saat refresh setelah setup
        try { window.E2EESession?.setR?.(Rb64, { persist: true, ttlMs: 30*24*60*60*1000 }); } catch (e) {}
        try { const mod = await import('../crypto/key-worker-client'); await mod.setR(Rb64, 30*24*60*60*1000); } catch (e) {}

        document.getElementById('recovery-code').value = recovery;
        document.getElementById('recovery-block').classList.remove('d-none');

        const confirmEl = document.getElementById('recovery-saved');
        const confirmSaved = async () => {
          if (!confirmEl.checked) {
            showCustomAlert('danger', 'Please confirm you saved the recovery code');
            return;
          }
          showCustomAlert('success', 'E2EE enabled');
          setTimeout(() => window.location.href = '/', 800);
        };
        confirmEl.addEventListener('change', confirmSaved, { once: true });
      } catch (err) {
        console.error(err);
        showCustomAlert('danger', 'Failed to enable E2EE');
        status.textContent = '';
      }
    });
  });
})();
