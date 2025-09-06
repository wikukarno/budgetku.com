<template>
  <div class="card bg-white border-0 rounded-3 mb-4">
    <div class="card-body p-4">
      <h3 class="mb-3">Enable End-to-End Encryption</h3>
      <p class="text-muted mb-4">Your encryption keys are generated and stored only on your device. Choose a strong passphrase.</p>

      <div class="mb-3">
        <label class="form-label">E2EE Passphrase</label>
        <input v-model="pass1" type="password" class="form-control" placeholder="Enter a strong passphrase" />
      </div>
      <div class="mb-4">
        <label class="form-label">Confirm Passphrase</label>
        <input v-model="pass2" type="password" class="form-control" placeholder="Re-enter passphrase" />
      </div>

      <div v-if="recovery" class="mb-4">
        <label class="form-label">Recovery Code (save this securely)</label>
        <div class="input-group">
          <input :value="recovery" type="text" class="form-control" readonly />
          <button class="btn btn-outline-secondary" @click="copyRecovery">Copy</button>
        </div>
        <small class="text-muted">This code can recover your keys if you forget the passphrase. We cannot restore it.</small>
      </div>

      <div class="d-flex gap-2">
        <button class="btn btn-primary" :disabled="busy" @click="onGenerate">
          <span v-if="busy" class="spinner-border spinner-border-sm me-2"></span>
          Generate & Enable
        </button>
        <a href="/" class="btn btn-light">Cancel</a>
      </div>

      <div class="mt-3 text-muted">{{ status }}</div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import axios from 'axios';
import { useE2EE } from '../../stores/e2ee.js';
import { useAlert, useLoading, useCrypto, useClipboard } from '../../composables/useUtils.js';

const pass1 = ref('');
const pass2 = ref('');
const status = ref('');
const recovery = ref('');

const e2eeStore = useE2EE();
const { showSuccess, showError } = useAlert();
const { isLoading: busy, withLoading } = useLoading();
const { randomBytes, base64Encode } = useCrypto();
const { copy } = useClipboard();

function b64toBytes(b64str) {
  const bin = atob(b64str);
  const bytes = new Uint8Array(bin.length);
  for (let i = 0; i < bin.length; i++) {
    bytes[i] = bin.charCodeAt(i);
  }
  return bytes;
}

async function deriveAesKey(password, saltB64, params) {
  const salt = typeof saltB64 === 'string' ? b64toBytes(saltB64) : saltB64;
  
  if (params?.kdf === 'argon2id' && window.argon2) {
    const result = await window.argon2.hash({
      pass: password,
      salt,
      type: window.argon2.ArgonType.Argon2id,
      mem: params.mem || 65536,
      time: params.time || 3,
      parallelism: params.parallelism || 1,
      hashLen: 32,
      raw: true
    });
    return crypto.subtle.importKey('raw', result.hash, { name: 'AES-GCM' }, false, ['encrypt', 'decrypt']);
  }
  
  const iterations = Math.max(310000, params?.iter || 0);
  const baseKey = await crypto.subtle.importKey('raw', new TextEncoder().encode(password), 'PBKDF2', false, ['deriveKey']);
  return crypto.subtle.deriveKey(
    { name: 'PBKDF2', salt, iterations, hash: 'SHA-256' },
    baseKey,
    { name: 'AES-GCM', length: 256 },
    false,
    ['encrypt', 'decrypt']
  );
}

async function aesGcmEncrypt(key, dataBytes) {
  const iv = randomBytes(12);
  const ciphertext = await crypto.subtle.encrypt({ name: 'AES-GCM', iv }, key, dataBytes);
  
  const result = new Uint8Array(iv.byteLength + ciphertext.byteLength);
  result.set(iv, 0);
  result.set(new Uint8Array(ciphertext), iv.byteLength);
  
  return base64Encode(result);
}

function generateRecoveryCode(length = 26) {
  const alphabet = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
  let result = '';
  const bytes = randomBytes(length);
  
  for (let i = 0; i < length; i++) {
    result += alphabet[bytes[i] % alphabet.length];
  }
  
  return result.match(/.{1,4}/g).join('-');
}

async function onGenerate() {
  if (!pass1.value || pass1.value !== pass2.value) {
    showError('Passphrase mismatch');
    return;
  }

  await withLoading(async () => {
    try {
      status.value = 'Generating keys...';
      
      // Generate storage key (R)
      const R = randomBytes(32);
      const Rb64 = base64Encode(R);
      
      // Generate PGP keypair
      const { privateKey, publicKey } = await window.openpgp.generateKey({
        type: 'ecc',
        curve: 'curve25519',
        userIDs: [{ name: 'User', email: 'user@example.com' }],
        passphrase: Rb64
      });

      status.value = 'Wrapping storage key...';
      
      // Generate recovery code dan salts
      const recoveryCode = generateRecoveryCode();
      recovery.value = recoveryCode;
      
      const passSalt = base64Encode(randomBytes(16));
      const recSalt = base64Encode(randomBytes(16));
      
      // Determine KDF parameters
      const kdfParams = window.argon2 
        ? { kdf: 'argon2id', mem: 65536, time: 3, parallelism: 1 }
        : { kdf: 'pbkdf2', iter: 310000, hash: 'SHA-256' };
      
      // Derive keys dan encrypt R
      const passwordKey = await deriveAesKey(pass1.value, passSalt, kdfParams);
      const recoveryKey = await deriveAesKey(recoveryCode, recSalt, kdfParams);
      
      const passWrap = await aesGcmEncrypt(passwordKey, R);
      const recWrap = await aesGcmEncrypt(recoveryKey, R);

      status.value = 'Saving to server...';
      
      // Save to server
      await axios.post('/e2ee/keys', {
        pgp_public_key: publicKey,
        pgp_private_key_armor: privateKey,
        e2ee_pass_wrap: passWrap,
        e2ee_pass_salt: passSalt,
        e2ee_rec_wrap: recWrap,
        e2ee_rec_salt: recSalt,
        e2ee_kdf_params: kdfParams,
      });

      // Set R di store
      e2eeStore.setR(Rb64);
      // Persist R untuk sesi mendatang via session key (cookie wrap)
      try { await e2eeStore.wrapWithSessionKey(); } catch (e) { console.warn('[E2EE Setup] wrapWithSessionKey failed:', e); }
      
      showSuccess('E2EE enabled successfully!');
      
    } catch (error) {
      console.error('[E2EE Setup] Failed:', error);
      showError('Failed to enable E2EE. Please try again.');
      status.value = '';
    }
  }, 'Setting up E2EE...');
}

async function copyRecovery() {
  const success = await copy(recovery.value);
  if (success) {
    showSuccess('Recovery code copied to clipboard');
  } else {
    showError('Failed to copy recovery code');
  }
}
</script>

<style scoped>
</style>
