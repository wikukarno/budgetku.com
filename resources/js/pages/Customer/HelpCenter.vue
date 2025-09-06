<template>
  <MainLayout title="Help Center - BudgetKu" page-title="Help Center">
    <div class="card bg-white border-0 rounded-3 mb-4">
      <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
          <h3 class="mb-0">Help Center</h3>
        </div>

        <form @submit.prevent="onSubmit">
          <div class="row">
            <div class="col-lg-6 col-sm-6">
              <div class="form-group mb-4">
                <label class="label text-secondary">Name</label>
                <input v-model.trim="form.name" type="text" class="form-control h-55" placeholder="Enter name" required />
              </div>
            </div>
            <div class="col-lg-6 col-sm-6">
              <div class="form-group mb-4">
                <label class="label text-secondary">Email</label>
                <input v-model.trim="form.email" type="email" class="form-control h-55" placeholder="Enter email" required />
              </div>
            </div>

            <div class="col-lg-12">
              <div class="form-group mb-4">
                <label class="label text-secondary">Message</label>
                <textarea v-model.trim="form.message" class="form-control" rows="5" placeholder="Type your message here..." required></textarea>
              </div>
            </div>

            <!-- Cloudflare Turnstile CAPTCHA -->
            <div class="col-lg-12 mb-3">
              <div class="cf-turnstile" :data-sitekey="turnstileSite" data-callback="turnstileCallback"></div>
            </div>

            <div class="col-lg-12">
              <div class="d-flex flex-wrap justify-content-end gap-3">
                <Link href="/pages/customer/dashboard" class="btn btn-danger py-2 px-4 fw-medium fs-16 text-white">Cancel</Link>
                <button :disabled="submitting" type="submit" class="btn btn-primary py-2 px-4 fw-medium fs-16 d-flex align-items-center gap-2">
                  <span v-if="!submitting">Send Now</span>
                  <span v-else class="spinner-border spinner-border-sm text-white" role="status" aria-hidden="true"></span>
                </button>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </MainLayout>
  <div id="customAlertWrapper" class="position-fixed top-0 end-0 p-3" style="z-index: 1050;"></div>
</template>

<script setup>
import MainLayout from '../../layouts/MainLayout.vue';
import { Link } from '@inertiajs/vue3';
import { onMounted, reactive, ref } from 'vue';

const props = defineProps({
  user: Object,
  turnstileSite: { type: String, default: '' },
});

const form = reactive({
  name: props.user?.name || '',
  email: props.user?.email || '',
  message: '',
});
const submitting = ref(false);
const cfToken = ref('');

// Expose callback for Turnstile
window.turnstileCallback = function (token) {
  try { cfToken.value = token || ''; } catch {}
};

function ensureTurnstileScript() {
  if (document.querySelector('script[data-turnstile]')) return;
  const s = document.createElement('script');
  s.src = 'https://challenges.cloudflare.com/turnstile/v0/api.js';
  s.async = true; s.defer = true; s.setAttribute('data-turnstile', '1');
  document.head.appendChild(s);
}

async function onSubmit() {
  if (!cfToken.value) {
    window.Swal?.fire({ icon: 'warning', title: 'Validation Error', text: 'Please complete the CAPTCHA verification.' });
    return;
  }
  submitting.value = true;
  try {
    const fd = new FormData();
    fd.append('name', form.name);
    fd.append('email', form.email);
    fd.append('message', form.message);
    fd.append('cf-turnstile-response', cfToken.value);
    const res = await window.axios.post('/pages/customer/help-center/send', fd);
    const ok = !!res.data?.success;
    await window.Swal?.fire({ icon: ok ? 'success' : 'error', title: ok ? 'Success' : 'Error', text: res.data?.message || '' });
    if (ok) window.location.href = '/pages/customer/dashboard';
  } catch (e) {
    await window.Swal?.fire({ icon: 'error', title: 'Error', text: 'An error occurred. Please try again.' });
  } finally {
    submitting.value = false;
  }
}

onMounted(() => {
  ensureTurnstileScript();
});
</script>

