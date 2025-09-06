<template>
  <div :class="['card border-0 shadow-sm h-100', cardClass]">
    <div class="card-body d-flex align-items-center">
      <div :class="['rounded-circle d-flex align-items-center justify-content-center me-3', iconWrapClass]" style="width:48px;height:48px;">
        <i class="material-symbols-outlined text-white">{{ icon }}</i>
      </div>
      <div>
        <div class="text-muted small mb-1">{{ label }}</div>
        <div class="fs-4 fw-semibold">{{ displayValue }}</div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { useNumberFormat } from '../composables/useUtils.js';

const props = defineProps({
  label: { type: String, required: true },
  value: { type: [Number, String], default: '-' },
  icon: { type: String, default: 'insights' },
  variant: { type: String, default: 'primary' }, // primary|success|danger|warning|info|secondary
  currency: { type: Boolean, default: false },
});

const { formatCurrency, formatNumber } = useNumberFormat();

const variants = {
  primary: { bg: 'bg-primary', subtle: 'bg-primary-subtle' },
  success: { bg: 'bg-success', subtle: 'bg-success-subtle' },
  danger: { bg: 'bg-danger', subtle: 'bg-danger-subtle' },
  warning: { bg: 'bg-warning', subtle: 'bg-warning-subtle' },
  info: { bg: 'bg-info', subtle: 'bg-info-subtle' },
  secondary: { bg: 'bg-secondary', subtle: 'bg-secondary-subtle' },
};

const v = variants[props.variant] || variants.primary;
const iconWrapClass = v.bg;
const cardClass = '';

const displayValue = computed(() => {
  if (props.currency && typeof props.value === 'number') {
    return formatCurrency(props.value);
  }
  if (typeof props.value === 'number') {
    return formatNumber(props.value);
  }
  return props.value ?? '-';
});
</script>

<style scoped>
.material-symbols-outlined { font-size: 22px; line-height: 1; }
</style>

