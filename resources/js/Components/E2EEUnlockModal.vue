<template>
  <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header border-0 pb-0">
          <h5 class="modal-title">
            <i class="ri-shield-keyhole-line me-2"></i>
            Unlock Encryption
          </h5>
        </div>
        
        <div class="modal-body">
          <p class="text-muted mb-4">
            Enter your E2EE passphrase to decrypt your financial data.
            <br><small>Your data is encrypted and safe on this device.</small>
          </p>
          
          <form @submit.prevent="handleUnlock">
            <div class="mb-3">
              <label class="form-label">E2EE Passphrase</label>
              <input 
                ref="passphraseInput"
                v-model="passphrase" 
                type="password" 
                class="form-control" 
                placeholder="Enter your passphrase"
                :class="{ 'is-invalid': error }"
                required 
              />
              <div v-if="error" class="invalid-feedback">{{ error }}</div>
            </div>
            
            <div class="d-flex gap-2">
              <button 
                type="submit" 
                class="btn btn-primary flex-grow-1" 
                :disabled="busy || !passphrase.trim()"
              >
                <span v-if="busy" class="spinner-border spinner-border-sm me-2"></span>
                <i v-else class="ri-lock-unlock-line me-2"></i>
                {{ busy ? 'Unlocking...' : 'Unlock' }}
              </button>
              
              <button 
                type="button" 
                class="btn btn-outline-secondary" 
                @click="$emit('cancel')"
                :disabled="busy"
              >
                Skip
              </button>
            </div>
          </form>
          
          <div class="mt-3">
            <small class="text-muted">
              <a href="#" @click.prevent="showRecoveryInput = !showRecoveryInput" class="text-decoration-none">
                Forgot passphrase? Use recovery code
              </a>
            </small>
          </div>
          
          <!-- Recovery Code Input -->
          <div v-if="showRecoveryInput" class="mt-3">
            <div class="border-top pt-3">
              <label class="form-label">Recovery Code</label>
              <input 
                v-model="recoveryCode" 
                type="text" 
                class="form-control" 
                placeholder="Enter your recovery code"
                :class="{ 'is-invalid': recoveryError }"
              />
              <div v-if="recoveryError" class="invalid-feedback">{{ recoveryError }}</div>
              
              <div class="mt-2">
                <button 
                  type="button" 
                  class="btn btn-warning btn-sm" 
                  @click="handleRecoveryUnlock"
                  :disabled="busy || !recoveryCode.trim()"
                >
                  <span v-if="busy" class="spinner-border spinner-border-sm me-1"></span>
                  Recover with Code
                </button>
              </div>
            </div>
          </div>
        </div>
        
        <div class="modal-footer border-0 pt-0">
          <div class="small text-muted">
            <i class="ri-information-line me-1"></i>
            This modal will appear when E2EE keys need to be unlocked
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, nextTick } from 'vue';
import { useE2EE } from '../stores/e2ee';
import { useAlert, useLoading } from '../composables/useUtils';

const emit = defineEmits(['success', 'cancel']);

const e2eeStore = useE2EE();
const { showError } = useAlert();
const { isLoading: busy, withLoading } = useLoading();

const passphrase = ref('');
const error = ref('');
const recoveryCode = ref('');
const recoveryError = ref('');
const showRecoveryInput = ref(false);
const passphraseInput = ref(null);

// Auto focus input saat modal muncul
onMounted(async () => {
  await nextTick();
  passphraseInput.value?.focus();
});

async function handleUnlock() {
  error.value = '';
  
  if (!passphrase.value.trim()) {
    error.value = 'Please enter your passphrase';
    return;
  }

  await withLoading(async () => {
    try {
      await e2eeStore.unlockWithPassword(passphrase.value);
      emit('success');
    } catch (err) {
      console.error('[E2EE Unlock] Failed:', err);
      error.value = 'Invalid passphrase. Please try again.';
      
      // Clear passphrase untuk security
      passphrase.value = '';
      
      // Focus kembali ke input
      await nextTick();
      passphraseInput.value?.focus();
    }
  });
}

async function handleRecoveryUnlock() {
  recoveryError.value = '';
  
  if (!recoveryCode.value.trim()) {
    recoveryError.value = 'Please enter your recovery code';
    return;
  }

  await withLoading(async () => {
    try {
      // TODO: Implement recovery unlock in E2EE store
      // await e2eeStore.unlockWithRecoveryCode(recoveryCode.value);
      
      showError('Recovery code unlock not implemented yet');
      
    } catch (err) {
      console.error('[E2EE Recovery] Failed:', err);
      recoveryError.value = 'Invalid recovery code. Please try again.';
      recoveryCode.value = '';
    }
  });
}
</script>

<style scoped>
.modal.show {
  animation: modalFadeIn 0.3s ease-out;
}

@keyframes modalFadeIn {
  from {
    opacity: 0;
    transform: scale(0.95);
  }
  to {
    opacity: 1;
    transform: scale(1);
  }
}

.modal-content {
  border: none;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
}
</style>