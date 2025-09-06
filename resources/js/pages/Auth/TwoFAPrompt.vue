<template>
  <MainLayout title="Verify Two-Factor" page-title="Two-Factor Authentication">
    <div class="row align-items-center justify-content-center" style="min-height:60vh;">
      <div class="col-lg-5 col-md-7 col-sm-10">
        <div class="card bg-white border-0 rounded-3 shadow-sm">
          <div class="card-body p-4">
            <h3 class="mb-2">Two-Factor Authentication</h3>
            <p class="text-muted mb-4">Please enter the 6-digit code from your authenticator app</p>

            <div v-if="errorMsg" class="alert alert-danger py-2">{{ errorMsg }}</div>

            <form @submit.prevent="submitOtp" id="verify-form">
              <div class="form-group mb-4">
                <label class="label text-secondary">Authentication Code</label>
                <div class="d-flex gap-2 justify-content-center">
                  <input v-for="n in 6" :key="n" type="text" inputmode="numeric" maxlength="1" class="form-control text-center" style="width:50px;height:55px;font-size:24px;" :ref="el => setOtpRef(el, n-1)" @input="onOtpInput($event,n-1)" @keydown.backspace="onOtpBackspace($event,n-1)" @paste.prevent="onOtpPaste($event)" />
                </div>
              </div>
              <button type="submit" class="btn btn-primary w-100" :disabled="loading">
                <span v-if="loading" class="spinner-border spinner-border-sm me-2"></span>
                <span>{{ loading ? 'Processing...' : 'Verify Code' }}</span>
              </button>
            </form>

            <div class="text-center mt-4">
              <button class="btn btn-outline-secondary w-100" @click="openRecovery" :disabled="loading">
                <i class="material-symbols-outlined me-1">vpn_key</i>
                Use Recovery Code
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Recovery Modal -->
    <div class="modal fade" id="recoveryModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Enter Recovery Code</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <p class="text-muted">Enter one of your 2FA recovery codes. This will only work once per code.</p>
            <input type="text" class="form-control text-center" maxlength="255" v-model="recoveryCode" placeholder="e.g. ABCD123456" />
            <div v-if="recoveryError" class="alert alert-danger mt-3 py-2">{{ recoveryError }}</div>
            <div v-if="recoverySuccess" class="alert alert-success mt-3 py-2">Recovery code verified successfully!</div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="button" class="btn btn-success" @click="submitRecovery" :disabled="loadingRecovery">
              <span v-if="loadingRecovery" class="spinner-border spinner-border-sm me-2"></span>
              <span>Verify Recovery Code</span>
            </button>
          </div>
        </div>
      </div>
    </div>
  </MainLayout>
</template>

<script setup>
import MainLayout from '../../layouts/MainLayout.vue';
import { ref, onMounted } from 'vue';

const loading = ref(false);
const errorMsg = ref('');
const otpRefs = Array.from({ length: 6 }, () => ref(null));

function setOtpRef(el, idx){ otpRefs[idx] = el; }
function getOtp(){ return otpRefs.map(el => (el && el.value ? el.value.replace(/\D/g,'') : (el ? el.value : ''))).join('').slice(0,6); }
function onOtpInput(e, idx){ const v = e.target.value.replace(/\D/g,''); e.target.value = v; if (v && idx < 5) otpRefs[idx+1]?.focus(); }
function onOtpBackspace(e, idx){ if (e.target.value === '' && idx > 0) otpRefs[idx-1]?.focus(); }
function onOtpPaste(e){ const paste = (e.clipboardData?.getData('text')||'').replace(/\D/g,''); paste.split('').forEach((ch,i)=>{ if (i<6 && otpRefs[i]) otpRefs[i].value = ch; }); if (otpRefs[Math.min(paste.length,5)]) otpRefs[Math.min(paste.length,5)].focus(); }

async function submitOtp(){
  errorMsg.value = '';
  const code = getOtp();
  if (code.length !== 6) { errorMsg.value = 'Code must be 6 digits'; return; }
  try {
    loading.value = true;
    const { data } = await window.axios.post('/2fa/verify/login', { code, source: 'otp' }, { headers: { 'Accept': 'application/json' } });
    if (data?.status && data?.redirect) { window.location.href = data.redirect; return; }
    errorMsg.value = data?.message || 'Invalid code. Please try again.';
  } catch (e) {
    errorMsg.value = e?.response?.data?.message || 'Invalid code. Please try again.';
  } finally { loading.value = false; }
}

const recoveryCode = ref('');
const loadingRecovery = ref(false);
const recoveryError = ref('');
const recoverySuccess = ref(false);
let recoveryModal;

function openRecovery(){
  recoveryCode.value=''; recoveryError.value=''; recoverySuccess.value=false;
  try {
    const el = document.getElementById('recoveryModal');
    recoveryModal = window.bootstrap?.Modal.getOrCreateInstance(el);
    recoveryModal?.show();
  } catch {}
}
async function submitRecovery(){
  recoveryError.value=''; recoverySuccess.value=false;
  try {
    loadingRecovery.value = true;
    const { data } = await window.axios.post('/2fa/verify/login', { code: recoveryCode.value, source: 'recovery' }, { headers: { 'Accept':'application/json' } });
    if (data?.status && data?.redirect) {
      recoverySuccess.value = true;
      setTimeout(()=> window.location.href = data.redirect, 800);
      return;
    }
    recoveryError.value = data?.message || 'Invalid recovery code';
  } catch (e) {
    recoveryError.value = e?.response?.data?.message || 'Invalid recovery code';
  } finally { loadingRecovery.value = false; }
}

onMounted(() => { try { otpRefs[0]?.focus(); } catch {} });
</script>

