<template>
  <div class="sidebar-area" id="sidebar-area">
    <div class="logo position-relative">
      <Link :href="isOwner ? '/pages/admin/dashboard' : '/pages/customer/dashboard'" class="d-block text-decoration-none position-relative">
        <span class="logo-text fw-bold text-dark">BudgetKu</span>
      </Link>
      <button class="sidebar-burger-menu bg-transparent p-0 border-0 opacity-0 z-n1 position-absolute top-50 end-0 translate-middle-y" id="sidebar-burger-menu">
        <i data-feather="x"></i>
      </button>
    </div>

    <aside id="layout-menu" class="layout-menu menu-vertical menu active" data-simplebar>
      <ul class="menu-inner">
        <li class="menu-title small text-uppercase"><span class="menu-title-text">MAIN</span></li>

        <!-- Dashboard -->
        <li class="menu-item" :class="{ open: isActive(isOwner ? '/pages/admin/dashboard' : '/pages/customer/dashboard') }">
          <Link :href="isOwner ? '/pages/admin/dashboard' : '/pages/customer/dashboard'" class="menu-link" :class="{ active: isActive(isOwner ? '/pages/admin/dashboard' : '/pages/customer/dashboard') }">
            <span class="material-symbols-outlined menu-icon">dashboard</span>
            <span class="title">Dashboard</span>
          </Link>
        </li>

        <li class="menu-title small text-uppercase"><span class="menu-title-text">CATEGORY</span></li>

        <template v-if="isOwner">
          <li class="menu-item" :class="{ open: isActive('/pages/admin/category/income') }">
            <Link href="/pages/admin/category/income" class="menu-link" :class="{ active: isActive('/pages/admin/category/income') }">
              <span class="material-symbols-outlined menu-icon">list_alt</span>
              <span class="title">Income Category</span>
            </Link>
          </li>
          <li class="menu-item" :class="{ open: isActive('/pages/admin/category/expense') }">
            <Link href="/pages/admin/category/expense" class="menu-link" :class="{ active: isActive('/pages/admin/category/expense') }">
              <span class="material-symbols-outlined menu-icon">list_alt</span>
              <span class="title">Expense Category</span>
            </Link>
          </li>
          <li class="menu-item" :class="{ open: isActive('/pages/admin/payment-method') }">
            <Link href="/pages/admin/payment-method" class="menu-link" :class="{ active: isActive('/pages/admin/payment-method') }">
              <span class="material-symbols-outlined menu-icon">credit_card</span>
              <span class="title">Payment Method</span>
            </Link>
          </li>
        </template>
        <template v-else>
          <li class="menu-item" :class="{ open: isActive('/pages/customer/category/income') }">
            <Link href="/pages/customer/category/income" class="menu-link" :class="{ active: isActive('/pages/customer/category/income') }">
              <span class="material-symbols-outlined menu-icon">list_alt</span>
              <span class="title">Income Category</span>
            </Link>
          </li>
          <li class="menu-item" :class="{ open: isActive('/pages/customer/category/expense') }">
            <Link href="/pages/customer/category/expense" class="menu-link" :class="{ active: isActive('/pages/customer/category/expense') }">
              <span class="material-symbols-outlined menu-icon">list_alt</span>
              <span class="title">Expense Category</span>
            </Link>
          </li>
          <li class="menu-item" :class="{ open: isActive('/pages/customer/payment-method') }">
            <Link href="/pages/customer/payment-method" class="menu-link" :class="{ active: isActive('/pages/customer/payment-method') }">
              <span class="material-symbols-outlined menu-icon">credit_card</span>
              <span class="title">Payment Method</span>
            </Link>
          </li>
        </template>

        <li class="menu-title small text-uppercase"><span class="menu-title-text">TRANSACTIONS</span></li>

        <template v-if="isOwner">
          <li class="menu-item" :class="{ open: isActive('/pages/admin/income') }">
            <Link href="/pages/admin/income" class="menu-link" :class="{ active: isActive('/pages/admin/income') }">
              <span class="material-symbols-outlined menu-icon">attach_money</span>
              <span class="title">Income</span>
            </Link>
          </li>
          <li class="menu-item" :class="{ open: isActive('/pages/admin/expense') }">
            <Link href="/pages/admin/expense" class="menu-link" :class="{ active: isActive('/pages/admin/expense') }">
              <span class="material-symbols-outlined menu-icon">payments</span>
              <span class="title">Expense</span>
            </Link>
          </li>
        </template>
        <template v-else>
          <li class="menu-item" :class="{ open: isActive('/pages/customer/income') }">
            <Link href="/pages/customer/income" class="menu-link" :class="{ active: isActive('/pages/customer/income') }">
              <span class="material-symbols-outlined menu-icon">attach_money</span>
              <span class="title">Income</span>
            </Link>
          </li>
          <li class="menu-item" :class="{ open: isActive('/pages/customer/expense') }">
            <Link href="/pages/customer/expense" class="menu-link" :class="{ active: isActive('/pages/customer/expense') }">
              <span class="material-symbols-outlined menu-icon">payments</span>
              <span class="title">Expense</span>
            </Link>
          </li>
        </template>

        <li class="menu-title small text-uppercase"><span class="menu-title-text">ACCOUNT</span></li>

        <li class="menu-item" :class="{ open: isActive(isOwner ? '/pages/admin/account' : '/pages/customer/account') }">
          <Link :href="isOwner ? '/pages/admin/account' : '/pages/customer/account'" class="menu-link" :class="{ active: isActive(isOwner ? '/pages/admin/account' : '/pages/customer/account') }">
            <span class="material-symbols-outlined menu-icon">person</span>
            <span class="title">Profile</span>
          </Link>
        </li>
        <li v-if="!isOwner" class="menu-item" :class="{ open: isActive('/pages/customer/help-center') }">
          <Link href="/pages/customer/help-center" class="menu-link" :class="{ active: isActive('/pages/customer/help-center') }">
            <span class="material-symbols-outlined menu-icon">email</span>
            <span class="title">Help Center</span>
          </Link>
        </li>

        <li class="menu-item">
          <a href="#" class="menu-link" @click.prevent="openLogoutModal">
            <span class="material-symbols-outlined menu-icon">logout</span>
            <span class="title">Log Out</span>
          </a>
        </li>
      </ul>
    </aside>
  </div>
</template>

<script setup>
import { computed, onMounted, onBeforeUnmount, watch } from 'vue';
import { Link, usePage, router } from '@inertiajs/vue3';

const page = usePage();
const isOwner = computed(() => (page.props.auth?.user?.roles || '') === 'Owner');

function isActive(pathPrefix) {
  const current = page.url || '';
  return current.startsWith(pathPrefix);
}

function openLogoutModal() {
  // Try use global provided by Header.vue
  if (typeof window.__openLogoutModal === 'function') {
    window.__openLogoutModal();
    return;
  }
  // Fallback: simple confirm then logout
  if (confirm('Log out of BudgetKu?')) {
    router.post('/logout');
  }
}

// --- Mobile: auto-close sidebar when clicking outside (Vue-level) ---
const STORAGE_KEY = 'bk_sidebar_theme';
const isSmall = () => window.innerWidth < 992;
const closeSidebar = () => {
  document.body.setAttribute('sidebar-data-theme', 'sidebar-hide');
  try { localStorage.setItem(STORAGE_KEY, 'sidebar-hide'); } catch {}
};
const onDocClick = (e) => {
  try {
    if (!isSmall()) return;
    const isShown = document.body.getAttribute('sidebar-data-theme') === 'sidebar-show';
    if (!isShown) return;
    const root = document.getElementById('sidebar-area');
    const inside = root?.contains(e.target);
    const isToggle = e.target?.closest?.('#header-burger-menu, #sidebar-burger-menu');
    if (!inside && !isToggle) closeSidebar();
  } catch {}
};
const onKey = (e) => { if (e.key === 'Escape' && isSmall()) closeSidebar(); };
onMounted(() => {
  if (!document.body.getAttribute('sidebar-data-theme')) {
    document.body.setAttribute('sidebar-data-theme', 'sidebar-hide');
  }
  document.addEventListener('click', onDocClick, true);
  document.addEventListener('touchstart', onDocClick, true);
  document.addEventListener('keydown', onKey);
});
onBeforeUnmount(() => {
  document.removeEventListener('click', onDocClick, true);
  document.removeEventListener('touchstart', onDocClick, true);
  document.removeEventListener('keydown', onKey);
});
watch(() => page.url, () => { if (isSmall()) closeSidebar(); });
</script>
