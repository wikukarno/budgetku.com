<template>
  <div>
    <Head>
      <title>{{ title || 'BudgetKu' }}</title>
      <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
      <meta name="csrf-token" :content="csrfToken" />
    </Head>

    <div>
      <!-- Sidebar -->
      <component :is="currentSidebar" />
      
      <!-- Main Content Area -->
      <div class="container-fluid">
        <div class="main-content d-flex flex-column">
          <!-- Header -->
          <Header />
          
          <!-- Content -->
          <div class="main-content-container overflow-hidden">
            <!-- Custom Alert Wrapper -->
            <div id="customAlertWrapper" class="position-fixed top-0 end-0 p-3" style="z-index: 1050;"></div>
            
            <!-- Page Content (no extra card wrapper to match v2 layout) -->
            <slot />
          </div>
        </div>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { Head } from '@inertiajs/vue3';
import Sidebar from '../Components/Sidebar.vue';
import AdminSidebar from '../Components/AdminSidebar.vue';
import Header from '../Components/Header.vue';
import { useV2UiInit } from '../composables/useV2UiInit.js';

const props = defineProps({
  title: { type: String, default: 'BudgetKu' },
  pageTitle: { type: String, default: 'Dashboard' }
});

// Get CSRF token
const csrfToken = computed(() => {
  const meta = document.querySelector('meta[name="csrf-token"]');
  return meta ? meta.getAttribute('content') : '';
});

// Determine which sidebar to show
const currentSidebar = computed(() => {
  const url = window.location.pathname || '';
  return url.startsWith('/pages/admin') ? AdminSidebar : Sidebar;
});


// Init v2 UI behaviors after mount and every Inertia navigation
useV2UiInit();
</script>
