<template>
  <AuthLayout title="BudgetKu - Login to your account">
    <div class="row align-items-center">
      <div class="col-lg-6 d-none d-lg-block">
        <img :src="bannerSrc" class="rounded-3" alt="login" />
      </div>
      <div class="col-lg-6">
        <div class="mw-480 ms-lg-auto">
          <div class="d-inline-block mb-4">
            <img :src="logoSrc" class="rounded-3 for-light-logo" alt="login" />
            <img :src="logoSrc" class="rounded-3 for-dark-logo" alt="login" />
          </div>
          <h3 class="fs-28 mb-2">Welcome back to BudgetKu</h3>
          <p class="text-muted fs-15 mb-4">Sign in with Google or your email and password.</p>
          
          <div class="row justify-content-start">
            <div class="col-lg-4 col-sm-4">
              <a href="/auth/redirect" class="btn btn-outline-secondary bg-transparent w-100 py-2 hover-bg mb-4" style="border-color:#D6DAE1;">
                <img :src="googleIcon" alt="google" />
              </a>
            </div>
          </div>

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

          <form @submit.prevent="onSubmit" id="login-form">
            <div class="form-group mb-2">
              <label class="label text-secondary">Email Address</label>
              <input 
                v-model.trim="form.email" 
                type="email" 
                class="form-control h-55" 
                placeholder="example@budgetku.com" 
                required
              />
            </div>
            <div class="form-group mb-3">
              <label class="label text-secondary">Password</label>
              <div class="position-relative password-field">
                <input 
                  v-model="form.password" 
                  :type="showPass ? 'text' : 'password'"
                  class="form-control h-55 pe-5" 
                  placeholder="Type password" 
                  required
                />
                <button type="button" class="password-toggle" @click="showPass = !showPass" :aria-label="showPass ? 'Hide password' : 'Show password'">
                  <i class="material-symbols-outlined">{{ showPass ? 'visibility_off' : 'visibility' }}</i>
                </button>
              </div>
              <div v-if="fieldError" class="text-danger small mt-1">{{ fieldError }}</div>
            </div>

          <div class="mb-3">
            <label class="d-inline-flex align-items-center mb-0">
              <input type="checkbox" v-model="form.e2eeRemember" class="form-check-input me-2" />
              <span class="small">Remember encryption on this device</span>
            </label>
            <small class="text-muted d-block mt-1" v-if="form.e2eeRemember">
              Keeps your encrypted key for 30 days and across logout on this device.
            </small>
            <small class="text-muted d-block mt-1" v-else>
              Encryption will only be kept for this session (cleared on logout or browser close).
            </small>
          </div>

          <div class="form-group mb-4">
            <button 
              type="submit" 
              class="btn btn-primary fw-medium py-2 px-3 w-100"
              :disabled="busy"
              aria-busy="busy ? 'true' : 'false'"
            >
              <div class="d-flex align-items-center justify-content-center py-1">
                <span v-if="busy" class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                <i v-else class="material-symbols-outlined text-white fs-20 me-2">login</i>
                <span>{{ busy ? 'Processing...' : 'Login' }}</span>
              </div>
            </button>
              <div class="d-inline-flex align-items-center text-success mt-2">
                <i class="ri-shield-keyhole-line me-2"></i>
                <small class="fw-medium">End-to-end encrypted</small>
              </div>
            </div>
          </form>

          <div class="text-center mt-2">
            <span class="text-muted fs-12">Don't have an account? </span>
            <Link href="/register" class="fw-medium text-decoration-none text-primary">Register</Link>
          </div>
        </div>
      </div>
    </div>
  </AuthLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import AuthLayout from '../../layouts/AuthLayout.vue';
import { useE2EE } from '../../stores/e2ee.js';
import { useValidation, useLoading } from '../../composables/useUtils.js';
import axios from 'axios';

const error = ref('');
const fieldError = ref('');
const showPass = ref(false);

const bannerSrc = '/banner.webp';
const logoSrc = '/v2/images/logo.svg';
const googleIcon = '/v2/images/google.svg';

// Form state (manual, using axios for login)
const form = {
  email: '',
  password: '',
  e2eeRemember: true,
};

const e2eeStore = useE2EE();
const { validateEmail, validatePassword } = useValidation();
const { isLoading: busy, withLoading } = useLoading();

async function autoRestoreE2EEAfterLogin() {
  try {
    // Coba unlock E2EE dengan password yang baru saja digunakan untuk login
    await e2eeStore.unlockWithPassword(form.password);
    console.log('[Login] E2EE automatically unlocked after login');
    // Wrap setelah unlock sukses agar sesi berikutnya tidak perlu passphrase
    try { await e2eeStore.wrapWithSessionKey(); } catch {}
    // Pastikan akunnya punya account-wrap agar bisa recover walau cookies hilang
    try { await e2eeStore.setAccountWrap(form.password); } catch {}
  } catch (error) {
    console.warn('[Login] Failed to auto-unlock E2EE:', error);
    // Coba restore via session-key + cookie wrap (remember without localStorage)
    try {
      const ok = await e2eeStore.restoreFromSessionKey();
      if (ok) {
        console.log('[Login] Restored E2EE from session key');
        // Refresh wrap to extend validity with the latest session key
        try { await e2eeStore.wrapWithSessionKey(); } catch {}
        try { await e2eeStore.setAccountWrap(form.password); } catch {}
      }
      else {
        // Terakhir coba unlock dengan account wrap (dibungkus oleh password login sebelumnya)
        try {
          const ok2 = await e2eeStore.unlockWithAccountPassword?.(form.password);
          if (ok2) {
            try { await e2eeStore.wrapWithSessionKey(); } catch {}
            try { await e2eeStore.setAccountWrap(form.password); } catch {}
          }
        } catch {}
      }
    } catch (e) { }
  }
}

async function onSubmit() {
  error.value = '';
  fieldError.value = '';
  if (busy.value) return; // prevent double submit
  
  // Basic validation
  if (!validateEmail(form.email)) {
    fieldError.value = 'Please enter a valid email';
    return;
  }
  
  if (!validatePassword(form.password, 8)) {
    fieldError.value = 'Password must be at least 8 characters';
    return;
  }

  await withLoading(async () => {
    try {
      const res = await axios.post('/login', {
        email: form.email,
        password: form.password,
      }, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });

      // Sukses login, sekarang urus E2EE di sisi klien sebelum pindah halaman
      try {
        await autoRestoreE2EEAfterLogin();
        // Setelah unlock/restore, bungkus R sesuai preferensi user (remember encryption)
        try { await e2eeStore.wrapWithSessionKey({ persist: !!form.e2eeRemember }); } catch {}
        try { await e2eeStore.saveDeviceWrap?.(); } catch {}
      } catch {}

      // Issue/rotate auth refresh cookie (bk_rt) so if AUTH cookie disappears we can rehydrate session silently
      try { await axios.post('/auth/refresh/issue'); } catch {}

      const redirect = res?.data?.redirect || '/';
      window.location.href = redirect;
    } catch (e) {
      console.error('[Login] Login failed:', e);
      const resp = e?.response;
      if (resp?.status === 422) {
        fieldError.value = resp?.data?.message || 'Invalid credentials';
      } else {
        error.value = 'Login failed. Please try again.';
      }
    }
  });
}
</script>

<style scoped>
</style>
<style scoped>
.password-toggle {
  position: absolute;
  right: 0.5rem;
  top: 50%;
  transform: translateY(-50%);
  width: 42px;
  height: 55px; /* match .h-55 */
  display: flex;
  align-items: center;
  justify-content: center;
  background: transparent;
  border: 0;
  padding: 0;
}
.password-toggle i { font-size: 20px; line-height: 1; }
.password-field { /* wrapper strictly around the input so centering is correct */ }
</style>
