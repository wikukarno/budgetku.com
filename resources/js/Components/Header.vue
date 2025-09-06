<template>
  <div class="header-area bg-white mb-4 rounded-bottom-15" id="header-area">
    <div class="row align-items-center">
      <div class="col-lg-4 col-sm-6">
        <div class="left-header-content">
          <ul class="d-flex align-items-center ps-0 mb-0 list-unstyled justify-content-center justify-content-sm-start">
            <li>
              <button class="header-burger-menu bg-transparent p-0 border-0" id="header-burger-menu">
                <span class="material-symbols-outlined">menu</span>
              </button>
            </li>
          </ul>
        </div>
      </div>

      <div class="col-lg-8 col-sm-6">
        <div class="right-header-content mt-2 mt-sm-0">
          <ul class="d-flex align-items-center justify-content-center justify-content-sm-end ps-0 mb-0 list-unstyled">
            <!-- E2EE Dev Indicator -->
            <li class="header-right-item me-2" v-if="showE2eeIndicator">
              <div class="d-inline-flex align-items-center px-2 py-1 rounded-2 border small"
                   :class="isUnlocked ? 'border-success text-success' : 'border-warning text-warning'"
                   title="E2EE status for debugging">
                <i class="material-symbols-outlined me-1" style="font-size:18px;">
                  {{ isUnlocked ? 'lock_open_right' : 'lock' }}
                </i>
                <span class="me-2">{{ isUnlocked ? 'Unlocked' : 'Locked' }}</span>
                <span class="text-muted" v-if="ttlMin > 0">TTL {{ ttlMin }}m</span>
                <span class="text-muted" v-else>TTL -</span>
                <span class="ms-2" :class="cookiePresent ? 'text-primary' : 'text-muted'">Cookie {{ cookiePresent ? '✓' : '×' }}</span>
              </div>
            </li>
            <!-- Light / Dark toggle -->
            <li class="header-right-item">
              <div class="light-dark">
                <button class="switch-toggle settings-btn dark-btn p-0 bg-transparent" id="switch-toggle">
                  <span class="dark"><i class="material-symbols-outlined">light_mode</i></span>
                  <span class="light"><i class="material-symbols-outlined">dark_mode</i></span>
                </button>
              </div>
            </li>

            <!-- Fullscreen toggle -->
            <li class="header-right-item">
              <button class="fullscreen-btn bg-transparent p-0 border-0" id="fullscreen-button">
                <i class="material-symbols-outlined text-body">fullscreen</i>
              </button>
            </li>

            <!-- User Dropdown -->
            <li class="header-right-item">
              <div class="dropdown admin-profile">
                <button type="button" class="d-xxl-flex align-items-center bg-transparent border-0 text-start p-0 cursor dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                  <div class="flex-shrink-0">
                    <img class="rounded-circle wh-40 administrator" :src="userAvatar" alt="admin" />
                  </div>
                  <div class="flex-grow-1 ms-2">
                    <div class="d-flex align-items-center justify-content-between">
                      <div class="d-none d-xxl-block">
                        <div class="d-flex align-content-center">
                          <h3>{{ userName }}</h3>
                        </div>
                      </div>
                    </div>
                  </div>
                </button>

                <div class="dropdown-menu border-0 bg-white dropdown-menu-end">
                  <div class="d-flex align-items-center info">
                    <div class="flex-shrink-0">
                      <img class="rounded-circle wh-30 administrator" :src="userAvatar" alt="admin" />
                    </div>
                    <div class="flex-grow-1 ms-2">
                      <h3 class="fw-medium">{{ userName }}</h3>
                      <span class="fs-12">{{ roleLabel }}</span>
                    </div>
                  </div>
                  <ul class="admin-link ps-0 mb-0 list-unstyled">
                    <li>
                      <Link :href="accountLink" class="dropdown-item d-flex align-items-center text-body">
                        <i class="material-symbols-outlined">account_circle</i>
                        <span class="ms-2">My Profile</span>
                      </Link>
                    </li>
                    <li>
                      <Link href="/e2ee/recover" class="dropdown-item d-flex align-items-center text-body">
                        <i class="material-symbols-outlined">key</i>
                        <span class="ms-2">Recover E2EE</span>
                      </Link>
                    </li>
                    <li>
                      <Link href="/e2ee/setup" class="dropdown-item d-flex align-items-center text-body">
                        <i class="material-symbols-outlined">vpn_key</i>
                        <span class="ms-2">Encryption Setup</span>
                      </Link>
                    </li>
                    <li>
                      <a href="#" class="dropdown-item d-flex align-items-center text-body" @click.prevent="lockNow">
                        <i class="material-symbols-outlined">lock</i>
                        <span class="ms-2">Lock Now</span>
                      </a>
                    </li>
                  </ul>
                  <ul class="admin-link ps-0 mb-0 list-unstyled">
                    <li>
                      <button type="button" @click="openLogoutModal" class="dropdown-item d-flex align-items-center text-body w-100 border-0 bg-transparent">
                        <i class="material-symbols-outlined">logout</i>
                        <span class="ms-2">Log Out</span>
                      </button>
                    </li>
                  </ul>
                </div>
              </div>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>

  <!-- Logout Confirmation Modal -->
  <div class="modal fade" id="logoutConfirm" tabindex="-1" aria-labelledby="logoutConfirmLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="logoutConfirmLabel">Confirm Logout</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          Are you sure you want to log out of BudgetKu? Your encrypted session will be locked.
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" @click="hideLogoutModal">Cancel</button>
          <button type="button" class="btn btn-danger text-white" @click="confirmLogout">
            Log Out
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted, watch, onBeforeUnmount } from 'vue';
import { Link, usePage, router } from '@inertiajs/vue3';
import { useE2EE } from '../stores/e2ee.js';

const page = usePage();
const e2eeStore = useE2EE();

// User data from page props
const user = computed(() => page.props.auth?.user || {});
const userName = computed(() => user.value.name || 'User');
const userEmail = computed(() => user.value.email || '');
const roleLabel = computed(() => user.value.roles || 'User');
const userAvatar = computed(() => {
  const avatar = user.value.avatar;
  if (avatar && String(avatar).trim().length > 0) {
    return avatar;
  }
  const baseUrl = window.location.origin;
  return `${baseUrl}/v2/images/administrator.jpg`;
});

// Route-aware account link
const accountLink = computed(() => {
  const url = (usePage().url || '');
  return url.startsWith('/pages/admin') ? '/pages/admin/account' : '/pages/customer/account';
});

function openLogoutModal() {
  try {
    if (window.$ && typeof window.$.fn?.modal === 'function') {
      window.$('#logoutConfirm').modal('show');
    } else if (window.bootstrap?.Modal) {
      const el = document.getElementById('logoutConfirm');
      const inst = window.bootstrap.Modal.getOrCreateInstance(el);
      inst.show();
    }
  } catch {}
}

function hideLogoutModal() {
  try {
    if (window.$ && typeof window.$.fn?.modal === 'function') {
      window.$('#logoutConfirm').modal('hide');
    } else if (window.bootstrap?.Modal) {
      const el = document.getElementById('logoutConfirm');
      const inst = window.bootstrap.Modal.getOrCreateInstance(el);
      inst.hide();
    }
  } catch {}
}

function confirmLogout() {
  try { window.lockE2EE?.(); } catch {}
  try { e2eeStore.lock(); } catch {}
  hideLogoutModal();
  router.post('/logout');
}

// Expose global to allow Sidebar trigger reuse
try { window.__openLogoutModal = openLogoutModal; } catch {}

function lockNow() {
  try {
    if (typeof window.lockE2EE === 'function') {
      window.lockE2EE();
      return;
    }
  } catch {}
  try { e2eeStore.lock(); } catch {}
}

// ---- Behavior init (dropdown, dark mode, fullscreen) ----
function initDropdowns() {
  try {
    const toggles = document.querySelectorAll('.admin-profile .dropdown-toggle');
    toggles.forEach((el) => {
      try {
        const DD = window.bootstrap?.Dropdown;
        if (DD) {
          const inst = DD.getOrCreateInstance(el);
          const onClick = (e) => {
            e.preventDefault();
            e.stopPropagation();
            try { inst.toggle(); } catch {}
          };
          el.addEventListener('click', onClick);
          cleanup.push(() => el.removeEventListener('click', onClick));
        } else {
          // Fallback manual toggle if Bootstrap not yet available
          const menu = el.parentElement?.querySelector('.dropdown-menu');
          if (!menu) return;
          const onClick = (e) => {
            e.preventDefault();
            e.stopPropagation();
            const parent = el.closest('.dropdown');
            const willShow = !menu.classList.contains('show');
            if (parent) parent.classList.toggle('show', willShow);
            el.setAttribute('aria-expanded', willShow ? 'true' : 'false');
            menu.classList.toggle('show', willShow);
            const close = (ev) => {
              if (parent && !parent.contains(ev.target)) {
                parent.classList.remove('show');
                menu.classList.remove('show');
                el.setAttribute('aria-expanded', 'false');
                document.removeEventListener('click', close);
              }
            };
            document.addEventListener('click', close);
          };
          el.addEventListener('click', onClick);
          cleanup.push(() => el.removeEventListener('click', onClick));
        }
      } catch {}
    });
  } catch {}
}

function initDarkMode() {
  const btn = document.getElementById('switch-toggle');
  if (!btn) return;
  const apply = (mode) => {
    try { document.body.setAttribute('data-theme', mode); } catch {}
  };
  const saved = localStorage.getItem('trezo_theme');
  if (saved) apply(saved);
  const onClick = (e) => {
    e.preventDefault();
    const cur = document.body.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
    localStorage.setItem('trezo_theme', cur);
    apply(cur);
  };
  btn.addEventListener('click', onClick);
  cleanup.push(() => btn.removeEventListener('click', onClick));
}

function initFullscreen() {
  const btn = document.getElementById('fullscreen-button');
  if (!btn) return;
  const onClick = (e) => {
    e.preventDefault();
    try {
      if (!document.fullscreenElement) {
        document.documentElement.requestFullscreen?.();
      } else {
        document.exitFullscreen?.();
      }
    } catch {}
  };
  btn.addEventListener('click', onClick);
  cleanup.push(() => btn.removeEventListener('click', onClick));
}

const cleanup = [];

function initHeaderBehaviors() {
  initDropdowns();
  initDarkMode();
  initFullscreen();
}

onMounted(() => {
  // Init now and on every Inertia navigation
  initHeaderBehaviors();
});

watch(() => usePage().url, () => {
  initHeaderBehaviors();
});

onBeforeUnmount(() => {
  cleanup.forEach((fn) => { try { fn(); } catch {} });
});

// ---- E2EE Dev Indicator ----
const showE2eeIndicator = computed(() => {
  try {
    // Tampilkan hanya jika user login dan E2EE enabled
    return !!user.value?.email && !!e2eeStore.e2eeEnabled.value;
  } catch { return false; }
});
const isUnlocked = computed(() => !!e2eeStore.isUnlocked.value);
const ttlMin = computed(() => {
  try {
    const exp = Number(e2eeStore.expiresAt.value || 0);
    if (!exp) return 0;
    const leftMs = Math.max(0, exp - Date.now());
    return Math.ceil(leftMs / 60000);
  } catch { return 0; }
});
function hasCookie(name) {
  try {
    return document.cookie.split(';').some(c => c.trim().startsWith(name + '='));
  } catch { return false; }
}
const cookiePresent = computed(() => hasCookie('bk_wr'));
</script>

<style scoped>
.header-area {
  padding: 25px;
  /* Biarkan CSS v2 yang mengatur offset sidebar; jangan set margin-left di sini */
  transition: all 0.3s ease;
}

.wh-40 { width: 40px; height: 40px; object-fit: cover; }
.wh-30 { width: 30px; height: 30px; object-fit: cover; }

.cursor {
  cursor: pointer;
}

.rounded-bottom-10 {
  border-bottom-left-radius: 10px;
  border-bottom-right-radius: 10px;
}
.admin-profile .dropdown-menu { min-width: 14rem; }
.admin-profile .dropdown-menu.show { display: block; z-index: 1055; }
</style>
