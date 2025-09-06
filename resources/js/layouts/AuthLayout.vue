<template>
  <div>
    <!-- Inertia head management -->
    <Head>
      <title>{{ title }}</title>
      <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
      <meta name="csrf-token" :content="csrfToken" />
    </Head>

    <!-- Main Content -->
    <div class="container">
      <div class="main-content d-flex flex-column p-0">
        <div class="m-auto m-1230">
          <slot />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Head } from '@inertiajs/vue3';
import { computed } from 'vue';

defineProps({
  title: {
    type: String,
    default: 'BudgetKu'
  }
});

// Get CSRF token
const csrfToken = computed(() => {
  const meta = document.querySelector('meta[name="csrf-token"]');
  return meta ? meta.getAttribute('content') : '';
});

// Helper untuk asset URLs
function asset(path) {
  const baseUrl = window.location.origin;
  return `${baseUrl}/${path}`;
}
</script>
