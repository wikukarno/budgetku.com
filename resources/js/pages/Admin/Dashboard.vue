<template>
  <MainLayout title="Admin Dashboard - BudgetKu" page-title="Admin Dashboard">
    <!-- Expenses Overview -->
    <div class="row justify-content-center mb-4">
      <div class="col-xl-12">
        <!-- OLD: <ExpenseChart :monthly-expenses="laporanBulananTahunIni || []" /> -->
        <ExpenseChart :monthly-expenses="chartMonthly" />
      </div>
    </div>

    <!-- Financial Overview Cards (match v2 style) -->
    <div class="row justify-content-center">
      <div class="col-xl-12">
        <div class="card bg-white border-0 rounded-3 mb-4">
          <div class="card-body p-4" style="padding-bottom: 0 !important;">
            <div class="mb-3 mb-lg-4">
              <h3 class="mb-0">Financial Overview</h3>
            </div>
            <div class="row">
              <FinancialStatCard
                v-for="c in cards"
                :key="c.title"
                :title="c.title"
                :value="c.value"
                :change="0"
                change-label=""
                :icon="c.icon"
                :variant="c.variant"
                :invert-change-color="!!c.invert"
                col-size="6"
              />
            </div>
          </div>
        </div>
      </div>
    </div>
  </MainLayout>
</template>

<script setup>
import { onMounted, ref } from 'vue';
import MainLayout from '../../layouts/MainLayout.vue';

import FinancialStatCard from '../../Components/FinancialStatCard.vue';
import ExpenseChart from '../../Components/ExpenseChart.vue';
import { useE2EE } from '../../stores/e2ee.js';

// OLD server-derived props (no longer reliable with encrypted content)
// const props = defineProps({
//   totalPendapatan: Number,
//   pengeluaran: Number,
//   categoryFinances: Number,
//   todayExpenditure: Number,
//   weeklyReport: Number,
//   anualReport: Number,
//   previeusYearReport: Number,
//   laporanTahunan: Number,
//   monthlyBills: Number,
//   yearlyBills: Number,
//   monthlyReport: Number,
//   keterangan: String,
//   laporanBulananTahunIni: Array,
//   kategoriBucin: Number,
// });

// Reactive stats computed from decrypted data
const cards = ref([
  { title: 'Total Balance', value: 0, icon: 'account_balance_wallet', variant: 'primary' },
  { title: 'Monthly Spending', value: 0, icon: 'stacks', variant: 'danger', invert: true },
  { title: 'Weekly Spending', value: 0, icon: 'calendar_view_week', variant: 'warning', invert: true },
  { title: 'Daily Spending', value: 0, icon: 'today', variant: 'info', invert: true },
]);

// Chart data: array of { month: 1..12, total: number }
const chartMonthly = ref([]);

const e2ee = useE2EE();

async function getDecPriv() {
  // Reuse pattern from other pages
  if (getDecPriv.cache) return getDecPriv.cache;
  await e2ee.fetchUserKeys();
  const keys = e2ee.userKeys?.value || null;
  if (!keys?.pgp_private_key_armor) throw new Error('No private key');
  let R = e2ee.Rb64?.value || null;
  try { if (!R) R = sessionStorage.getItem('e2ee_R_b64') || null; } catch {}
  if (!R) throw new Error('E2EE is locked');
  const priv = await window.openpgp.readPrivateKey({ armoredKey: keys.pgp_private_key_armor });
  const dec = await window.openpgp.decryptKey({ privateKey: priv, passphrase: R });
  getDecPriv.cache = dec; return dec;
}

async function decryptAmount(plain, armor) {
  try {
    if (plain === '[encrypted]' && armor) {
      const priv = await getDecPriv();
      const msg = await window.openpgp.readMessage({ armoredMessage: armor });
      const { data } = await window.openpgp.decrypt({ message: msg, decryptionKeys: priv });
      return Number(String(data || '').replace(/[^\d]/g, '')) || 0;
    }
    return Number(String(plain || '').replace(/[^\d]/g, '')) || 0;
  } catch {
    return 0;
  }
}

function parseDate(d) {
  if (!d) return null;
  try { return new Date(d); } catch { return null; }
}

function inSameDay(a, b) { return a.getFullYear()===b.getFullYear() && a.getMonth()===b.getMonth() && a.getDate()===b.getDate(); }
function isInCurrentWeek(d) {
  const now = new Date();
  const day = now.getDay(); // 0 Sun..6 Sat
  const diffToMon = (day + 6) % 7; // Monday as start
  const monday = new Date(now); monday.setDate(now.getDate() - diffToMon); monday.setHours(0,0,0,0);
  const sunday = new Date(monday); sunday.setDate(monday.getDate() + 6); sunday.setHours(23,59,59,999);
  return d >= monday && d <= sunday;
}
function isInCurrentMonth(d) { const n=new Date(); return d.getFullYear()===n.getFullYear() && d.getMonth()===n.getMonth(); }
function isInCurrentYear(d) { const n=new Date(); return d.getFullYear()===n.getFullYear(); }

async function loadAndCompute() {
  try {
    // Fetch lists (include encrypted fields)
    const [incRes, expRes] = await Promise.all([
      window.axios.get('/pages/admin/income/list'),
      window.axios.get('/pages/admin/expense/list'),
    ]);
    const incomes = Array.isArray(incRes.data) ? incRes.data : [];
    const expenses = Array.isArray(expRes.data) ? expRes.data : [];

    // Decrypt amounts
    const decIncomes = await Promise.all(incomes.map(async (r) => ({
      date: r.date,
      amount: await decryptAmount(r.salary, r.salary_pgp),
    })));
    const decExpenses = await Promise.all(expenses.map(async (r) => ({
      date: r.purchase_date,
      amount: await decryptAmount(r.price, r.price_pgp),
    })));

    // Totals for cards
    const today = new Date(); today.setHours(0,0,0,0);
    let daily = 0, weekly = 0, monthly = 0;
    for (const e of decExpenses) {
      const d = parseDate(e.date); if (!d) continue;
      if (inSameDay(d, today)) daily += e.amount;
      if (isInCurrentWeek(d)) weekly += e.amount;
      if (isInCurrentMonth(d)) monthly += e.amount;
    }

    // Total balance: gunakan semua data (Income - Expense) agar tidak nol/salah
    // OLD windowed logic (2 months) dikomentari agar mudah di-restore bila perlu.
    // const now = new Date();
    // const firstOfPrevMonth = new Date(now.getFullYear(), now.getMonth()-1, 1);
    // const endOfCurrentMonth = new Date(now.getFullYear(), now.getMonth()+1, 0, 23,59,59,999);
    // const recentIncomes = decIncomes.filter(r => {
    //   const d = parseDate(r.date); return d && d >= firstOfPrevMonth && d <= endOfCurrentMonth; 
    // });
    // const minIncomeDate = recentIncomes.reduce((min, r) => {
    //   const d = parseDate(r.date); if (!d) return min; return (!min || d < min) ? d : min;
    // }, null);
    // const sumIncome = recentIncomes.reduce((sum, r) => sum + (r.amount||0), 0);
    // let sumExpenseRange = 0;
    // if (minIncomeDate) {
    //   for (const e of decExpenses) {
    //     const d = parseDate(e.date); if (!d) continue;
    //     if (d >= minIncomeDate && d <= endOfCurrentMonth) sumExpenseRange += e.amount || 0;
    //   }
    // }
    const sumIncomeAll = decIncomes.reduce((s, r) => s + (r.amount||0), 0);
    const sumExpenseAll = decExpenses.reduce((s, r) => s + (r.amount||0), 0);
    let totalBalance = sumIncomeAll - sumExpenseAll; // all-time fallback (kept for reference)

    // Chart monthly for current year
    const monthlyTotals = Array(12).fill(0);
    for (const e of decExpenses) {
      const d = parseDate(e.date); if (!d) continue;
      if (isInCurrentYear(d)) monthlyTotals[d.getMonth()] += e.amount || 0;
    }
    chartMonthly.value = monthlyTotals.map((total, idx) => ({ month: idx+1, total }));

    // Monthly net balance = monthly income - monthly expense
    const monthlyIncome = decIncomes.reduce((sum, r) => {
      const d = parseDate(r.date); return d && isInCurrentMonth(d) ? sum + (r.amount||0) : sum;
    }, 0);
    totalBalance = monthlyIncome - monthly;

    // Update cards
    cards.value = [
      { title: 'Total Balance', value: totalBalance, icon: 'account_balance_wallet', variant: 'primary' },
      { title: 'Monthly Spending', value: monthly, icon: 'stacks', variant: 'danger', invert: true },
      { title: 'Weekly Spending', value: weekly, icon: 'calendar_view_week', variant: 'warning', invert: true },
      { title: 'Daily Spending', value: daily, icon: 'today', variant: 'info', invert: true },
    ];
  } catch (err) {
    console.error('Failed to load dashboard stats', err);
  }
}

onMounted(async () => {
  try { await e2ee.fetchUserKeys(); } catch {}
  await loadAndCompute();
});
</script>
