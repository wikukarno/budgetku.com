<template>
  <MainLayout title="My Account - BudgetKu" page-title="My Account">
    <div class="row justify-content-center">
      <div class="col-lg-12">
        <div class="card bg-primary border-0 rounded-3 welcome-box style-two mb-4 position-relative">
          <div class="card-body py-38 px-4">
            <div class="mb-5">
              <h3 class="text-white fw-semibold">Welcome Back, <span class="text-danger-div">{{ user?.name }}!</span></h3>
              <p class="text-light">Have a nice and blessed day.</p>
            </div>
          </div>
          <img :src="asset('v2/images/welcome-2.gif')" class="welcome-2 d-none d-sm-block" alt="welcome" />
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-xxl-12">
        <div class="row">
          <div class="col-xxl-12 col-md-6 col-lg-6">
            <div class="card bg-white border-0 rounded-3 mb-4">
              <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
                  <h3 class="mb-0">Account Settings</h3>
                </div>

                <ul class="nav nav-tabs mb-4" id="settingsTab" role="tablist">
                  <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="account-tab" data-bs-toggle="tab" data-bs-target="#account" type="button" role="tab">Account Settings</button>
                  </li>
                  <li class="nav-item" role="presentation">
                    <button class="nav-link" id="password-tab" data-bs-toggle="tab" data-bs-target="#password" type="button" role="tab">Change Password</button>
                  </li>
                  <li class="nav-item" role="presentation">
                    <button class="nav-link" id="twofa-tab" data-bs-toggle="tab" data-bs-target="#twofa" type="button" role="tab">Two-Factor Authentication</button>
                  </li>
                  <li class="nav-item" role="presentation">
                    <button class="nav-link" id="delete-tab" data-bs-toggle="tab" data-bs-target="#delete" type="button" role="tab">Delete Account</button>
                  </li>
                </ul>

                <div class="tab-content" id="settingsTabContent">
                  <!-- Account Settings -->
                  <div class="tab-pane fade show active" id="account" role="tabpanel">
                    <div class="mb-4">
                      <h4 class="fs-20 mb-1">Profile</h4>
                      <p class="fs-15">Update your photo and personal details here.</p>
                    </div>
                    <div class="alert alert-info mb-3">Manage your encryption recovery below.</div>
                    <div class="mb-4 d-flex flex-wrap gap-2 btn-toolbar" role="toolbar" aria-label="Recovery toolbar">
                      <button type="button" class="btn btn-outline-primary me-2" id="btn-download-recovery">
                        <i class="material-symbols-outlined">download</i>
                        Generate & Download Recovery File
                      </button>
                      <button type="button" class="btn btn-outline-secondary" id="btn-recovery-info">
                        <i class="material-symbols-outlined">info</i>
                        How recovery works
                      </button>
                    </div>
                    <form id="account-form" :action="`/pages/admin/account/update/${user?.uuid}`" method="POST" @submit.prevent="submitProfile">
                      <input type="hidden" name="_method" value="PUT" />
                      <div class="row">
                        <div class="col-lg-6">
                          <div class="form-group mb-4">
                            <label class="label text-secondary">Name</label>
                            <div class="form-group position-relative">
                              <input type="text" name="name" :value="user?.name" class="form-control text-dark ps-5 h-55" required />
                              <i class="ri-user-line position-absolute top-50 start-0 translate-middle-y fs-20 text-gray-light ps-20"></i>
                            </div>
                          </div>
                        </div>
                        <div class="col-lg-6">
                          <div class="form-group mb-4">
                            <label class="label text-secondary">Email Address</label>
                            <div class="form-group position-relative">
                              <input type="email" name="email" :value="user?.email" class="form-control text-dark ps-5 h-55" readonly />
                              <i class="ri-mail-line position-absolute top-50 start-0 translate-middle-y fs-20 text-gray-light ps-20"></i>
                            </div>
                          </div>
                        </div>
                        <div class="col-lg-6">
                          <div class="form-group mb-4">
                            <label class="label text-secondary">Parent Email (Optional)</label>
                            <div class="form-group position-relative">
                              <input type="email" name="email_parrent" :value="user?.email_parrent || ''" class="form-control text-dark ps-5 h-55" />
                              <i class="ri-mail-unread-line position-absolute top-50 start-0 translate-middle-y fs-20 text-gray-light ps-20"></i>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="text-end">
                        <button type="submit" class="btn btn-primary px-4">Update Profile</button>
                      </div>
                    </form>
                  </div>

                  <!-- Change Password -->
                  <div class="tab-pane fade" id="password" role="tabpanel">
                    <div class="mb-4">
                      <h4 class="fs-20 mb-1">Change Password</h4>
                      <p class="fs-15">Update your account password securely.</p>
                    </div>
                    <form id="change-password-form" action="/pages/admin/account/password/update" method="POST" @submit.prevent="submitPassword">
                      <input type="hidden" name="_method" value="PUT" />
                      <div class="mb-3">
                        <label class="label">Current Password</label>
                        <input type="password" name="current_password" class="form-control" required />
                      </div>
                      <div class="mb-3">
                        <label class="label">New Password</label>
                        <input type="password" name="new_password" class="form-control" required />
                      </div>
                      <div class="mb-4">
                        <label class="label">Confirm New Password</label>
                        <input type="password" name="new_password_confirmation" class="form-control" required />
                      </div>
                      <button type="submit" class="btn btn-primary">Update Password</button>
                    </form>
                  </div>

                  <!-- Two-Factor Authentication -->
                  <div class="tab-pane fade" id="twofa" role="tabpanel">
                    <div class="mb-4">
                      <h4 class="fs-20 mb-1">Two-Factor Authentication</h4>
                      <p class="fs-15 text-muted">Add an extra layer of security to your account using Google Authenticator.</p>
                    </div>
<button class="btn btn-primary" id="btn-enable-2fa" v-show="!twoFAEnabled" @click="showModalTwoFA">
                      <i class="bi bi-shield-lock"></i> Enable Two-Factor Authentication
                    </button>
<div id="disable-2fa-section" v-show="twoFAEnabled">
                      <form id="disable-2fa-form" @submit.prevent="disable2FA">
                        <button type="submit" class="btn btn-danger">
                          <i class="bi bi-x-circle"></i> Disable Two-Factor Authentication
                        </button>
                      </form>
                    </div>
                  </div>

                  <!-- Delete Account -->
                  <div class="tab-pane fade" id="delete" role="tabpanel">
                    <div class="mb-4">
                      <h4 class="fs-20 text-danger mb-1">Delete Account</h4>
                      <p class="fs-15">Once deleted, your account and all data will be permanently removed. This action cannot be undone.</p>
                    </div>
                    <form id="delete-account-form" @submit.prevent="deleteAccount">
                      <p class="text-muted mb-3">
                        Once deleted, your account and all associated data will be <strong>permanently removed</strong>. This action cannot be undone.
                      </p>
                      <button type="submit" class="btn btn-danger text-white">Delete Account</button>
                    </form>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- 2FA Modal -->
    <div class="modal fade" id="enableTwoFactor" tabindex="-1" aria-labelledby="enableTwoFactorLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h1 class="modal-title fs-5" id="enableTwoFactorLabel"></h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form id="enable-2fa-form" method="POST">
            <div class="modal-body">
              <div id="enable-2fa-section"></div>
              <div id="qr-section" hidden>
                <div class="text-center mb-3"><img id="qr-code-img" src="" alt="QR Code" class="img-fluid" /></div>
                <p class="text-center">Secret Key: <strong id="secret-key"></strong></p>
              </div>
              <div id="otp-section" hidden>
                <div class="mb-3">
                  <label for="otp-code" class="form-label">Enter the 6-digit code from your Authenticator App</label>
                  <input type="text" class="form-control text-center" id="otp-code" maxlength="6" placeholder="123456" required />
                </div>
              </div>
              <div id="recovery-section" hidden>
                <hr />
                <h6 class="text-center mb-2">Recovery Codes</h6>
                <p class="text-muted text-center">Please save these recovery codes securely.</p>
                <ul id="recovery-codes" class="list-group mb-3 text-center"></ul>
                <div class="text-center">
                  <button type="button" class="btn btn-outline-primary btn-sm mb-2" id="btn-download-2fa-codes">Download Codes</button><br />
                  <small class="text-muted">You can close this modal after saving.</small>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-danger text-white" id="btn-cancel-2fa">Cancel</button>
              <button type="button" id="btnNext2FA" class="btn btn-primary">Next</button>
              <button type="button" id="btnVerify2FA" class="btn btn-success text-white" hidden>Verify and Enable 2FA</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </MainLayout>
  <div id="customAlertWrapper" style="position: fixed; top: 80px; right: 24px; z-index: 2000;"></div>

  <!-- Recovery Information Modal -->
  <div class="modal fade" id="recoveryInfoModal" tabindex="-1" aria-labelledby="recoveryInfoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content border-0 shadow-lg">
        <div class="modal-header bg-primary text-white border-0">
          <h1 class="modal-title fs-4 fw-semibold" id="recoveryInfoModalLabel">
            <i class="material-symbols-outlined me-2 align-middle">security</i>
            Recovery Information
          </h1>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body p-4">
          <div class="text-center mb-4">
            <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
              <i class="material-symbols-outlined text-primary" style="font-size: 40px;">shield</i>
            </div>
            <h4 class="mt-3 mb-2">Secure Data Recovery</h4>
            <p class="text-muted">Your recovery code is a secure backup that lets you regain access to your encrypted data if you forget your password.</p>
          </div>
          
          <div class="row g-4 mb-4">
            <div class="col-md-6">
              <div class="card border-0 bg-light h-100">
                <div class="card-body text-center">
                  <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 50px; height: 50px;">
                    <i class="material-symbols-outlined text-success">download</i>
                  </div>
                  <h6 class="fw-semibold">Generate & Download</h6>
                  <p class="small text-muted mb-0">Creates a new recovery file and automatically downloads it to your device securely.</p>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="card border-0 bg-light h-100">
                <div class="card-body text-center">
                  <div class="bg-warning bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 50px; height: 50px;">
                    <i class="material-symbols-outlined text-warning">warning</i>
                  </div>
                  <h6 class="fw-semibold">Important Notes</h6>
                  <p class="small text-muted mb-0">Generating a new recovery code will invalidate any previous recovery codes.</p>
                </div>
              </div>
            </div>
          </div>
          
          <div class="alert alert-warning border-0 bg-warning bg-opacity-10">
            <div class="d-flex align-items-start">
              <i class="material-symbols-outlined text-warning me-3 mt-1">folder_special</i>
              <div>
                <strong class="text-warning">Keep your recovery file safe!</strong>
                <p class="mb-0 text-muted small mt-1">Store it in a secure location like a password manager or encrypted backup. Anyone with this file can access your encrypted data.</p>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer border-0 pt-0 pb-4 px-4">
          <div class="d-flex gap-2 w-100">
            <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">
              <i class="material-symbols-outlined me-1">close</i>
              Close
            </button>
            <button type="button" class="btn btn-primary px-4 flex-fill" onclick="window.generateRecoveryAndDownload?.(); document.querySelector('[data-bs-dismiss=modal]')?.click();">
              <i class="material-symbols-outlined me-1">download</i>
              Generate Recovery File Now
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import MainLayout from '../../layouts/MainLayout.vue';
import { onMounted, ref } from 'vue';

const props = defineProps({ user: Object, twoFactorEnabled: Boolean });
const user = props.user || {};
// Gunakan state reaktif agar v-show bisa berubah real-time
const twoFAEnabled = ref(!!props.twoFactorEnabled);
function asset(path) { return `/` + path.replace(/^\//, ''); }

async function submitProfile() {
  const form = document.getElementById('account-form');
  const btn = form?.querySelector('button[type="submit"]');
  try {
    if (btn) { btn.disabled = true; btn.dataset.orig = btn.innerHTML; btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Saving...'; }
    const fd = new FormData(form);
    fd.append('_method', 'PUT');
    const res = await window.axios.post(form.getAttribute('action'), fd);
    if (res.data?.status) window.showCustomAlert?.('success', res.data.message || 'Profile updated');
    else window.showCustomAlert?.('danger', res.data?.message || 'Failed to update profile');
  } catch { window.showCustomAlert?.('danger', 'Something went wrong!'); }
  finally { if (btn) { btn.disabled = false; btn.innerHTML = btn.dataset.orig || 'Update Profile'; delete btn.dataset.orig; } }
}

async function submitPassword() {
  const form = document.getElementById('change-password-form');
  const btn = form?.querySelector('button[type="submit"]');
  try {
    if (btn) { btn.disabled = true; btn.dataset.orig = btn.innerHTML; btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Updating...'; }
    const fd = new FormData(form);
    const res = await window.axios.post(form.getAttribute('action'), fd, { params: { _method: 'PUT' } });
    if (res.data?.status) {
      window.showCustomAlert?.('success', res.data.message || 'Password updated');
      form.reset();
      try { await window.e2eeRotateAfterPasswordChange(fd.get('current_password'), fd.get('new_password')); } catch {}
    } else {
      window.showCustomAlert?.('danger', res.data?.message || 'Failed to update password');
    }
  } catch (err) {
    window.showCustomAlert?.('danger', err?.response?.data?.message || 'Something went wrong!');
  } finally { if (btn) { btn.disabled = false; btn.innerHTML = btn.dataset.orig || 'Update Password'; delete btn.dataset.orig; } }
}

function showModalTwoFA() {
  try {
    if (window.$ && typeof window.$.fn?.modal === 'function') {
      window.$('#enableTwoFactor').modal('show');
    } else if (window.bootstrap?.Modal) {
      const el = document.getElementById('enableTwoFactor');
      const inst = window.bootstrap.Modal.getOrCreateInstance(el);
      inst.show();
    }
  } catch {}
  const form = document.getElementById('enable-2fa-form');
  const fd = new FormData(form);
  window.axios.post('/2fa/setup', fd).then(res => {
    window.showCustomAlert?.('success', 'QR Code generated. Please scan and verify!');
    document.getElementById('qr-code-img').src = res.data.qr_code;
    document.getElementById('secret-key').textContent = res.data.secret;
    document.getElementById('qr-section')?.removeAttribute('hidden');
    document.getElementById('otp-section')?.setAttribute('hidden', 'true');
    document.getElementById('btnVerify2FA')?.setAttribute('hidden', 'true');
    document.getElementById('btnNext2FA')?.removeAttribute('hidden');
  }).catch(() => window.showCustomAlert?.('danger', 'Failed to enable 2FA'));
}

function disable2FA() {
  const form = document.getElementById('disable-2fa-form');
  const fd = new FormData(form);
  window.Swal?.fire({ title:'Are you sure?', text:'This will disable 2FA on your account.', icon:'warning', showCancelButton:true, confirmButtonColor:'#d33', cancelButtonColor:'#6c757d', confirmButtonText:'Yes, disable it' }).then(r => {
    if (!r.isConfirmed) return;
    window.axios.post('/2fa/disable', fd).then(() => {
      window.showCustomAlert?.('success', 'Two-Factor Authentication disabled.');
      // Update state reaktif agar tampilan tombol berubah langsung
      try { twoFAEnabled.value = false; } catch {}
    }).catch(() => window.showCustomAlert?.('danger', 'Failed to disable 2FA'));
  });
}

function deleteAccount() {
  window.Swal?.fire({ title:'Are you sure?', text:'Your account will be permanently deleted!', icon:'warning', showCancelButton:true, confirmButtonColor:'#d33', cancelButtonColor:'#6c757d', confirmButtonText:'Yes, delete it!' }).then(result => {
    if (!result.isConfirmed) return;
    window.axios.post('/pages/admin/account/delete', null, { params: { _method: 'DELETE' } }).then(res => {
      if (res.data?.status) {
        window.Swal.fire({ icon:'success', title:'Deleted!', text: res.data.message, timer: 2000, showConfirmButton:false }).then(() => {
          window.location.href = '/login';
        });
      } else {
        window.Swal.fire('Error', res.data?.message || 'Failed', 'error');
      }
    }).catch(() => window.Swal.fire('Error', 'Something went wrong!', 'error'));
  });
}

onMounted(() => {
  try {
    const hash = window.location.hash;
    if (hash) {
      const tabTrigger = document.querySelector(`button[data-bs-target="${hash}"]`);
      if (tabTrigger && window.bootstrap?.Tab) new window.bootstrap.Tab(tabTrigger).show();
    }
    const tabButtons = document.querySelectorAll('#settingsTab button[data-bs-toggle="tab"]');
    tabButtons.forEach((button) => {
      button.addEventListener('shown.bs.tab', function (e) {
        const target = e.target.getAttribute('data-bs-target');
        history.replaceState(null, null, target);
      });
    });
  } catch {}

  // Wire 2FA modal buttons
  try {
    function resetTwoFAModal() {
      try {
        document.getElementById('qr-code-img').src = '';
        document.getElementById('secret-key').textContent = '';
        const otp = document.getElementById('otp-code'); if (otp) otp.value = '';
        const list = document.getElementById('recovery-codes'); if (list) list.innerHTML = '';
        document.getElementById('recovery-section')?.setAttribute('hidden', 'true');
        document.getElementById('otp-section')?.setAttribute('hidden', 'true');
        document.getElementById('qr-section')?.setAttribute('hidden', 'true');
        // Reset tombol ke kondisi awal pembukaan modal
        document.getElementById('btnVerify2FA')?.setAttribute('hidden', 'true');
        document.getElementById('btnNext2FA')?.removeAttribute('hidden');
        document.getElementById('btn-cancel-2fa')?.removeAttribute('hidden');
      } catch {}
    }

    const btnNext = document.getElementById('btnNext2FA');
    const btnVerify = document.getElementById('btnVerify2FA');
    const btnCancel = document.getElementById('btn-cancel-2fa');
    const btnDownload = document.getElementById('btn-download-2fa-codes');
    // Reset saat modal ditutup apapun caranya
    try {
      const modalEl = document.getElementById('enableTwoFactor');
      modalEl?.addEventListener('hidden.bs.modal', resetTwoFAModal);
    } catch {}
    btnNext?.addEventListener('click', () => {
      document.getElementById('qr-section')?.setAttribute('hidden', 'true');
      document.getElementById('otp-section')?.removeAttribute('hidden');
      btnVerify?.removeAttribute('hidden');
      btnNext?.setAttribute('hidden', 'true');
    });
    btnVerify?.addEventListener('click', async () => {
      const code = (document.getElementById('otp-code')?.value || '').trim();
      if (code.length !== 6) { window.showCustomAlert?.('danger', 'OTP code must be 6 digits'); return; }
      const fd = new FormData(); fd.append('code', code);
      try {
        const res = await window.axios.post('/2fa/verify', fd);
        const list = document.getElementById('recovery-codes');
        list.innerHTML = '';
        (res.data?.recovery_codes || []).forEach(c => {
          const li = document.createElement('li'); li.className = 'list-group-item'; li.textContent = c; list.appendChild(li);
        });
        document.getElementById('otp-section')?.setAttribute('hidden', 'true');
        document.getElementById('recovery-section')?.removeAttribute('hidden');
        // Sembunyikan tombol aksi agar tidak ada re-verify/regenerate
        document.getElementById('btnVerify2FA')?.setAttribute('hidden', 'true');
        document.getElementById('btnNext2FA')?.setAttribute('hidden', 'true');
        document.getElementById('btn-cancel-2fa')?.setAttribute('hidden', 'true');
        window.showCustomAlert?.('success', '2FA enabled');
      } catch (e) {
        window.showCustomAlert?.('danger', e?.response?.data?.message || 'Invalid OTP');
      }
    });
    btnCancel?.addEventListener('click', () => {
      try {
        resetTwoFAModal();
        if (window.$ && typeof window.$.fn?.modal === 'function') {
          window.$('#enableTwoFactor').modal('hide');
        } else if (window.bootstrap?.Modal) {
          const el = document.getElementById('enableTwoFactor');
          const inst = window.bootstrap.Modal.getOrCreateInstance(el);
          inst.hide();
        }
      } catch {}
    });
    btnDownload?.addEventListener('click', async () => {
      const listItems = document.querySelectorAll('#recovery-codes li');
      if (!listItems.length) { window.showCustomAlert?.('danger', 'No recovery codes to download'); return; }
      const codes = Array.from(listItems).map(li => li.textContent);
      const blob = new Blob([codes.join('\n')], { type: 'text/plain' });
      const link = document.createElement('a'); link.href = URL.createObjectURL(blob); link.download = 'recovery-codes.txt';
      document.body.appendChild(link); link.click(); document.body.removeChild(link);
      window.showCustomAlert?.('success', 'Recovery codes downloaded.');
      try { await window.axios.post('/2fa/mark-downloaded'); } catch {}

      // Setelah download: tutup modal dan sinkronkan tampilan tombol Enable/Disable
      try {
        // Update state reaktif ke aktif agar v-show beralih ke Disable
        try { twoFAEnabled.value = true; } catch {}

        if (window.$ && typeof window.$.fn?.modal === 'function') {
          window.$('#enableTwoFactor').modal('hide');
        } else if (window.bootstrap?.Modal) {
          const el = document.getElementById('enableTwoFactor');
          const inst = window.bootstrap.Modal.getOrCreateInstance(el);
          inst.hide();
        }
      } catch {}
    });
  } catch {}

  // Bind E2EE recovery buttons (match Customer account)
  try {
    document.getElementById('btn-download-recovery')?.addEventListener('click', () => window.generateRecoveryAndDownload?.());
    document.getElementById('btn-recovery-info')?.addEventListener('click', () => window.showCurrentRecoveryInfo?.());
  } catch {}
});
</script>
