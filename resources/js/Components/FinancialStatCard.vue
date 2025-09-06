<template>
  <div :class="['col-xxl-' + colSize, 'col-xl-' + colSize, 'col-sm-' + (colSize === '12' ? '12' : '6')]">
    <div :class="[
      'card rounded-3 mb-4 stats-box style-three',
      `bg-${variant} bg-opacity-10 border-${variant} border-opacity-10`
    ]">
      <div class="card-body p-4">
        <div class="d-flex align-items-center mb-19">
          <div class="flex-shrink-0">
            <i :class="['material-symbols-outlined fs-40', `text-${variant}`]">{{ icon }}</i>
          </div>
          <div class="flex-grow-1 ms-2">
            <span>{{ title }}</span>
            <h3 class="fs-20 mt-1 mb-0">{{ formattedValue }}</h3>
          </div>
        </div>
        <div class="d-flex justify-content-between flex-wrap gap-2 align-items-center">
          <span class="fs-12">{{ changeLabel }}</span>
          <span :class="['count fw-medium ms-0', changeClass]">
            {{ formattedChange }}
          </span>
        </div>
        <div v-if="description" class="text-muted fs-12 mt-1">
          {{ description }}
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { useNumberFormat } from '../composables/useUtils.js';

const props = defineProps({
  title: {
    type: String,
    required: true
  },
  value: {
    type: Number,
    required: true
  },
  change: {
    type: Number,
    default: 0
  },
  changeLabel: {
    type: String,
    required: true
  },
  icon: {
    type: String,
    required: true
  },
  variant: {
    type: String,
    required: true,
    validator: (value) => ['primary', 'success', 'danger', 'warning', 'info'].includes(value)
  },
  colSize: {
    type: String,
    default: '6',
    validator: (value) => ['6', '12'].includes(value)
  },
  description: {
    type: String,
    default: null
  },
  invertChangeColor: {
    type: Boolean,
    default: false
  }
});

const { formatCurrency } = useNumberFormat();

const formattedValue = computed(() => formatCurrency(props.value));

const formattedChange = computed(() => {
  const sign = props.change >= 0 ? '+' : '';
  return `${sign}${props.change.toFixed(1)}%`;
});

const changeClass = computed(() => {
  const isPositive = props.change >= 0;
  
  // Untuk spending/pengeluaran, warna terbalik (merah jika naik, hijau jika turun)
  if (props.invertChangeColor) {
    return isPositive ? 'down' : 'up';
  }
  
  // Untuk income/balance, warna normal (hijau jika naik, merah jika turun)
  return isPositive ? 'up' : 'down';
});
</script>

<style scoped>
.mb-19 {
  margin-bottom: 19px;
}

.fs-40 {
  font-size: 40px;
}

.fs-20 {
  font-size: 20px;
}

.fs-12 {
  font-size: 12px;
}

.count.up {
  color: #28a745 !important;
}

.count.down {
  color: #dc3545 !important;
}
</style>