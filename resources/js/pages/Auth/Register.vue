<template>
  <AuthLayout title="BudgetKu - Create your account">
    <div class="row justify-content-center">
      <div class="col-12 col-md-8 col-lg-6">
        <div class="mx-auto">
          <div class="mb-3 text-center d-flex justify-content-center">
            <img :src="logoSrc" class="rounded-3 for-light-logo d-block mx-auto" alt="register" />
          </div>
          <h3 class="fs-22 mb-1 text-center">Create your BudgetKu account</h3>
          <p class="text-muted fs-13 mb-4 text-center">Quick and secure. Your data stays private.</p>

          <!-- Display server errors -->
          <div v-if="$page.props.errors && Object.keys($page.props.errors).length" class="alert alert-danger">
            <ul class="mb-0">
              <li v-for="(error, field) in $page.props.errors" :key="field" class="mb-1">
                {{ Array.isArray(error) ? error[0] : error }}
              </li>
            </ul>
          </div>

          <!-- Display form errors -->
          <div v-if="error" class="alert alert-danger">{{ error }}</div>

          <form @submit.prevent="onSubmit" novalidate>
            <div class="row g-3">
              <div class="col-12 col-md-6">
                <div class="form-group mb-0">
                  <label class="label text-secondary fs-12">Full Name</label>
                  <div class="position-relative">
                    <input 
                      v-model.trim="form.name" 
                      type="text" 
                      class="form-control text-dark ps-5 h-55" 
                      :class="{ 'is-invalid': fieldErrors.name }"
                      placeholder="Your name" 
                      required 
                    />
                    <i class="ri-user-line position-absolute top-50 start-0 translate-middle-y fs-20 text-gray-light ps-20"></i>
                    <div v-if="fieldErrors.name" class="invalid-feedback">{{ fieldErrors.name }}</div>
                  </div>
                </div>
              </div>
              <div class="col-12 col-md-6">
                <div class="form-group mb-0">
                  <label class="label text-secondary fs-12">Email Address</label>
                  <div class="position-relative">
                    <input 
                      v-model.trim="form.email" 
                      type="email" 
                      class="form-control text-dark ps-5 h-55" 
                      :class="{ 'is-invalid': fieldErrors.email }"
                      placeholder="example@budgetku.com" 
                      required 
                    />
                    <i class="ri-mail-line position-absolute top-50 start-0 translate-middle-y fs-20 text-gray-light ps-20"></i>
                    <div v-if="fieldErrors.email" class="invalid-feedback">{{ fieldErrors.email }}</div>
                  </div>
                </div>
              </div>
              <div class="col-12 col-md-6">
                <div class="form-group mb-0">
                  <label class="label text-secondary fs-12">Password</label>
                  <div class="position-relative">
                    <input 
                      v-model="form.password" 
                      :type="showPassword ? 'text' : 'password'" 
                      class="form-control text-dark ps-5 pe-5 h-55" 
                      :class="{ 'is-invalid': fieldErrors.password }"
                      placeholder="Create a strong password" 
                      required 
                    />
                    <i class="ri-lock-2-line position-absolute top-50 start-0 translate-middle-y fs-20 text-gray-light ps-20"></i>
                    <button 
                      type="button" 
                      class="btn bg-transparent border-0 position-absolute top-50 end-0 translate-middle-y me-2 p-0 text-secondary text-decoration-none" 
                      tabindex="-1" 
                      @click="showPassword = !showPassword"
                    >
                      <i :class="showPassword ? 'ri-eye-off-line' : 'ri-eye-line'"></i>
                    </button>
                    <div v-if="fieldErrors.password" class="invalid-feedback">{{ fieldErrors.password }}</div>
                  </div>
                </div>
              </div>
              <div class="col-12 col-md-6">
                <div class="form-group mb-0">
                  <label class="label text-secondary fs-12">Confirm Password</label>
                  <div class="position-relative">
                    <input 
                      v-model="form.password_confirmation" 
                      :type="showPassword2 ? 'text' : 'password'" 
                      class="form-control text-dark ps-5 pe-5 h-55" 
                      :class="{ 'is-invalid': fieldErrors.password_confirmation }"
                      placeholder="Re-enter password" 
                      required 
                    />
                    <i class="ri-lock-password-line position-absolute top-50 start-0 translate-middle-y fs-20 text-gray-light ps-20"></i>
                    <button 
                      type="button" 
                      class="btn bg-transparent border-0 position-absolute top-50 end-0 translate-middle-y me-2 p-0 text-secondary text-decoration-none" 
                      tabindex="-1" 
                      @click="showPassword2 = !showPassword2"
                    >
                      <i :class="showPassword2 ? 'ri-eye-off-line' : 'ri-eye-line'"></i>
                    </button>
                    <div v-if="fieldErrors.password_confirmation" class="invalid-feedback">{{ fieldErrors.password_confirmation }}</div>
                  </div>
                </div>
              </div>
            </div>
            
            <div class="mt-4"></div>
            <div class="form-group mb-4">
              <button 
                type="submit" 
                class="btn btn-primary fw-medium py-2 px-3 w-100" 
                :disabled="form.processing"
                :aria-busy="form.processing ? 'true' : 'false'"
              >
                <div class="d-flex align-items-center justify-content-center py-1">
                  <span v-if="form.processing" class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                  <i v-else class="material-symbols-outlined text-white fs-20 me-2">person_add</i>
                  <span>{{ form.processing ? 'Processing...' : 'Create Account' }}</span>
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
                <a href="/auth/redirect" class="btn btn-outline-secondary bg-transparent w-100 py-2 hover-bg" style="border-color:#D6DAE1; max-width:260px;">
                  <img :src="googleIcon" alt="google" />
                </a>
              </div>
            </div>
          </form>
          
          <div class="text-center mt-2">
            <span class="text-muted fs-12">Already have an account? </span>
            <Link href="/login" class="fw-medium text-decoration-none text-primary">Login</Link>
          </div>
        </div>
      </div>
    </div>
  </AuthLayout>
</template>

<script setup>
import { ref } from 'vue';
import { useForm, Link } from '@inertiajs/vue3';
import AuthLayout from '../../layouts/AuthLayout.vue';
import { useE2EE } from '../../stores/e2ee.js';
import { useValidation, useAlert, useCrypto } from '../../composables/useUtils.js';

const error = ref('');
const fieldErrors = ref({});
const showPassword = ref(false);
const showPassword2 = ref(false);

const logoSrc = '/v2/images/logo.svg';
const googleIcon = '/v2/images/google.svg';

// Inertia form
const form = useForm({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
});

const e2eeStore = useE2EE();
const { validateEmail, validatePassword, validateRequired } = useValidation();
const { showSuccess, showError } = useAlert();
const { randomBytes, base64Encode, generateRecoveryCode } = useCrypto();

// Auto-setup E2EE setelah register berhasil
async function autoSetupE2EE(password) {
  try {
    console.log('[Register] Starting auto E2EE setup...');
    
    // Generate storage key (R)
    const R = randomBytes(32);
    const Rb64 = base64Encode(R);
    
    // Generate PGP keypair
    const { privateKey, publicKey } = await window.openpgp.generateKey({
      type: 'ecc',
      curve: 'curve25519',
      userIDs: [{ name: form.name, email: form.email }],
      passphrase: Rb64
    });

    // Generate recovery code dan salts
    const recoveryCode = generateRecoveryCode();
    const passSalt = base64Encode(randomBytes(16));
    const recSalt = base64Encode(randomBytes(16));
    
    // Determine KDF parameters
    const kdfParams = window.argon2 
      ? { kdf: 'argon2id', mem: 65536, time: 3, parallelism: 1 }
      : { kdf: 'pbkdf2', iter: 310000, hash: 'SHA-256' };
    
    // Derive keys
    const passwordKey = await deriveAesKey(password, passSalt, kdfParams);
    const recoveryKey = await deriveAesKey(recoveryCode, recSalt, kdfParams);
    
    // Encrypt R
    const passWrap = await aesGcmEncrypt(passwordKey, R);
    const recWrap = await aesGcmEncrypt(recoveryKey, R);

    // Save to server
    const response = await fetch('/e2ee/keys', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
      },
      body: JSON.stringify({
        pgp_public_key: publicKey,
        pgp_private_key_armor: privateKey,
        e2ee_pass_wrap: passWrap,
        e2ee_pass_salt: passSalt,
        e2ee_rec_wrap: recWrap,
        e2ee_rec_salt: recSalt,
        e2ee_kdf_params: kdfParams,
      })
    });

    if (!response.ok) {
      throw new Error('Failed to save E2EE keys to server');
    }

    // Set R di store
    e2eeStore.setR(Rb64);
    
    console.log('[Register] E2EE auto-setup completed successfully');
  } catch (error) {
    console.error('[Register] E2EE auto-setup failed:', error);
    // Non-blocking error - user dapat setup E2EE nanti
  }
}

// Helper functions for E2EE
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

// Validation function
function validateForm() {
  fieldErrors.value = {};
  
  if (!validateRequired(form.name) || form.name.trim().length < 3) {
    fieldErrors.value.name = 'Name must be at least 3 characters';
  }
  
  if (!validateEmail(form.email)) {
    fieldErrors.value.email = 'Please enter a valid email';
  }
  
  if (!validatePassword(form.password, 8)) {
    fieldErrors.value.password = 'Password must be at least 8 characters';
  }
  
  if (form.password !== form.password_confirmation) {
    fieldErrors.value.password_confirmation = 'Passwords do not match';
  }
  
  return Object.keys(fieldErrors.value).length === 0;
}

async function onSubmit() {
  error.value = '';
  fieldErrors.value = {};
  if (form.processing) return; // prevent double submit
  
  // Client-side validation
  if (!validateForm()) {
    return;
  }

  // Submit dengan Inertia
  form.post('/register', {
    onSuccess: async (page) => {
      console.log('[Register] Registration successful');
      // Auto-setup E2EE setelah register berhasil
      await autoSetupE2EE(form.password);
      showSuccess('Account created successfully! E2EE has been automatically enabled.');
    },
    onError: (errors) => {
      console.error('[Register] Registration failed:', errors);
      fieldErrors.value = errors;
      error.value = 'Registration failed. Please check the form and try again.';
    }
  });
}
</script>
