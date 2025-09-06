<template>
  <div class="card bg-white border-0 rounded-3 mb-4">
    <div class="card-body p-4">
      <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3 mb-lg-4">
        <h3 class="mb-0">Expenses Overview</h3>
      </div>
      
      <div style="margin-top: -25px; margin-left: -10px; margin-bottom: -25px;">
        <div ref="chartContainer"></div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';

const props = defineProps({
  monthlyExpenses: {
    type: Array,
    required: true,
    default: () => []
  }
});

const chartContainer = ref(null);
let chart = null;

const monthNames = [
  'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
  'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
];

function initChart() {
  if (!chartContainer.value) return;

  // Inisialisasi array dengan 12 nol untuk setiap bulan di tahun ini
  let expenseData = Array(12).fill(0);

  // Isi data dengan total pengeluaran dari database
  props.monthlyExpenses.forEach(expense => {
    // Indeks array 0 adalah Januari, sehingga perlu dikurangi 1 dari bulan (1-12)
    expenseData[expense.month - 1] = expense.total;
  });

  const options = {
    series: [{
      data: expenseData
    }],
    chart: {
      type: 'bar',
      height: 350
    },
    plotOptions: {
      bar: {
        borderRadius: 4,
        borderRadiusApplication: 'end',
        horizontal: true,
      }
    },
    dataLabels: {
      enabled: false
    },
    tooltip: {
      y: {
        title: {
          formatter: function(val) {
            return '';
          }
        },
        formatter: function(val) {
          return 'Rp.' + val.toFixed(0).replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,');
        }
      }
    },
    xaxis: {
      categories: monthNames,
      labels: {
        formatter: function(val) {
          return 'Rp.' + val.toFixed(0).replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,');
        }
      }
    }
  };

  // Destroy existing chart
  if (chart) {
    chart.destroy();
  }

  // Create new chart
  chart = new ApexCharts(chartContainer.value, options);
  chart.render();
}

// Watch for changes in monthly expenses data
watch(() => props.monthlyExpenses, () => {
  initChart();
}, { deep: true });

onMounted(() => {
  // Wait for ApexCharts to be available
  const checkApexCharts = () => {
    if (typeof ApexCharts !== 'undefined') {
      initChart();
    } else {
      setTimeout(checkApexCharts, 100);
    }
  };
  
  checkApexCharts();
});
</script>

<style scoped>
.mb-3 {
  margin-bottom: 1rem;
}

.mb-lg-4 {
  margin-bottom: 1.5rem;
}

@media (min-width: 992px) {
  .mb-lg-4 {
    margin-bottom: 2rem;
  }
}
</style>