<template>
  <MainLayout title="Dashboard - BudgetKu" page-title="Dashboard">
    <!-- Expense Chart -->
    <div class="row justify-content-center mb-4">
      <div class="col-xl-12">
        <!-- OLD: <ExpenseChart :monthly-expenses="laporanBulananTahunIni" /> -->
        <ExpenseChart :monthly-expenses="chartMonthly" />
      </div>
    </div>

    <!-- Financial Overview -->
    <div class="row justify-content-center">
      <div class="col-xl-12">
        <div class="card bg-white border-0 rounded-3 mb-4">
          <div class="card-body p-4" style="padding-bottom: 0 !important;">
            <div class="mb-3 mb-lg-4">
              <h3 class="mb-0">Financial Overview</h3>
            </div>
            <div class="row">
              <!-- Total Balance -->
              <FinancialStatCard
                title="Total Balance"
                :value="totalSaldoX"
                :change="balanceChangeX"
                change-label="Compared to last month"
                icon="account_balance_wallet"
                variant="primary"
                col-size="6"
              />

              <!-- Monthly Spending -->
              <FinancialStatCard
                title="Monthly Spending"
                :value="pengeluaranBulanIniX"
                :change="spendingChangeX"
                change-label="Compared to last month"
                icon="stacks"
                variant="danger"
                col-size="6"
                :invert-change-color="true"
              />

              <!-- Monthly Income -->
              <FinancialStatCard
                title="Monthly Income"
                :value="gajiBulanIniX"
                :change="incomeChangeX"
                change-label="Compared to last month"
                icon="attach_money"
                variant="success"
                col-size="12"
                :description="`Total income for ${currentMonthYear}`"
              />

              <!-- Weekly Spending -->
              <FinancialStatCard
                title="Weekly Spending"
                :value="pengeluaranMingguIniX"
                :change="weeklyChangeX"
                change-label="Compared to last week"
                icon="calendar_view_week"
                variant="warning"
                col-size="6"
                :invert-change-color="true"
              />

              <!-- Daily Spending -->
              <FinancialStatCard
                title="Daily Spending"
                :value="pengeluaranHariIniX"
                :change="dailyChangeX"
                change-label="Compared to yesterday"
                icon="today"
                variant="info"
                col-size="6"
                :invert-change-color="true"
              />
            </div>
          </div>
        </div>
      </div>
    </div>
  </MainLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import MainLayout from '../../layouts/MainLayout.vue';
import FinancialStatCard from '../../Components/FinancialStatCard.vue';
import ExpenseChart from '../../Components/ExpenseChart.vue';
import { useE2EE } from '../../stores/e2ee.js';

// Props dari server (fallback awal bila dekripsi belum tersedia)
const props = defineProps({
  gajiBulanIni: { type: Number, default: 0 },
  gajiBulanLalu: { type: Number, default: 0 },
  pengeluaranBulanIni: { type: Number, default: 0 },
  pengeluaranBulanLalu: { type: Number, default: 0 },
  laporanBulananTahunIni: { type: Array, default: () => [] },
  pengeluaranMingguIni: { type: Number, default: 0 },
  pengeluaranHariIni: { type: Number, default: 0 },
  pengeluaranKemarin: { type: Number, default: 0 },
  pengeluaranMingguLalu: { type: Number, default: 0 },
  dailyChange: { type: Number, default: 0 },
  weeklyChange: { type: Number, default: 0 },
  saldoBulanIni: { type: Number, default: 0 },
  spendingChange: { type: Number, default: 0 },
  balanceChange: { type: Number, default: 0 },
  incomeChange: { type: Number, default: 0 },
  totalSaldo: { type: Number, default: 0 },
});

// Current month and year for description
const currentMonthYear = computed(() => {
  const now = new Date();
  return now.toLocaleDateString('id-ID', { month: 'long', year: 'numeric' });
});

// ==== Overlay dari data terdekripsi (Income) ====
const e2ee = useE2EE();
// Seed nilai awal dari server agar tidak nol saat belum unlock
const gajiBulanIniX = ref(props.gajiBulanIni);
const gajiBulanLaluX = ref(props.gajiBulanLalu);
const incomeChangeX = ref(props.incomeChange);
const totalSaldoX = ref(props.totalSaldo ?? (props.gajiBulanIni - props.pengeluaranBulanIni));
const balanceChangeX = ref(props.balanceChange);
const pengeluaranBulanIniX = ref(props.pengeluaranBulanIni);
const pengeluaranBulanLaluX = ref(props.pengeluaranBulanLalu);
const pengeluaranMingguIniX = ref(props.pengeluaranMingguIni);
const pengeluaranMingguLaluX = ref(props.pengeluaranMingguLalu);
const pengeluaranHariIniX = ref(props.pengeluaranHariIni);
const pengeluaranKemarinX = ref(props.pengeluaranKemarin);
const dailyChangeX = ref(props.dailyChange);
const weeklyChangeX = ref(props.weeklyChange);
const spendingChangeX = ref(props.spendingChange);
const chartMonthly = ref((props.laporanBulananTahunIni || []).map(r => ({ month: r.month, total: Number(r.total) || 0 })));

async function getDecPriv() {
  if (getDecPriv.cache) return getDecPriv.cache;
  
  // Fetch keys if not available
  await e2ee.fetchUserKeys();
  const keys = e2ee.userKeys?.value || null;
  if (!keys?.pgp_private_key_armor) {
    console.warn('[Customer Dashboard] No private key available');
    throw new Error('No private key');
  }
  
  let R = e2ee.Rb64?.value || null;
  try { 
    if (!R) R = sessionStorage.getItem('e2ee_R_b64') || null; 
  } catch {}
  
  if (!R) {
    console.warn('[Customer Dashboard] E2EE is locked');
    throw new Error('E2EE is locked');
  }
  
  console.log('[Customer Dashboard] Decrypting private key...');
  const priv = await window.openpgp.readPrivateKey({ armoredKey: keys.pgp_private_key_armor });
  const dec = await window.openpgp.decryptKey({ privateKey: priv, passphrase: R });
  getDecPriv.cache = dec; 
  console.log('[Customer Dashboard] Private key decrypted successfully');
  return dec;
}

async function decryptAmount(plain, armor) {
  try {
    if (plain === '[encrypted]' && armor) {
      console.log('[Customer Dashboard] Decrypting amount...');
      const priv = await getDecPriv();
      const msg = await window.openpgp.readMessage({ armoredMessage: armor });
      const { data } = await window.openpgp.decrypt({ message: msg, decryptionKeys: priv });
      const amount = Number(String(data || '').replace(/[^\d]/g, '')) || 0;
      console.log('[Customer Dashboard] Decrypted amount:', amount);
      return amount;
    }
    return Number(String(plain || '').replace(/[^\d]/g, '')) || 0;
  } catch (error) { 
    console.warn('[Customer Dashboard] Failed to decrypt amount:', error.message);
    return 0; 
  }
}

// Helper functions for date manipulation

async function ensureUnlocked() {
  try {
    const keys = await e2ee.fetchUserKeys();
    if (!keys || !keys.e2ee_enabled) return true; // nothing to unlock
    if (e2ee.isUnlocked?.value) return true;
    if (!window.Swal) return false;
    const { value: pass } = await window.Swal.fire({
      title: 'Unlock Encryption',
      text: 'Enter your E2EE passphrase to load stats',
      input: 'password',
      inputLabel: 'Passphrase',
      inputAttributes: { autocapitalize: 'off' },
      showCancelButton: true,
      confirmButtonText: 'Unlock'
    });
    if (!pass) return false;
    try { await e2ee.unlockWithPassword(pass); } catch { return false; }
    return !!e2ee.isUnlocked?.value;
  } catch { return false; }
}

function parseDate(d) {
  if (!d) return null;
  try { return new Date(d); } catch { return null; }
}

function inSameDay(a, b) { 
  if (!a || !b) return false;
  return a.getFullYear()===b.getFullYear() && a.getMonth()===b.getMonth() && a.getDate()===b.getDate(); 
}

function isInCurrentWeek(d) {
  if (!d) return false;
  const now = new Date();
  const day = now.getDay(); // 0 Sun..6 Sat
  const diffToMon = (day + 6) % 7; // Monday as start
  const monday = new Date(now); monday.setDate(now.getDate() - diffToMon); monday.setHours(0,0,0,0);
  const sunday = new Date(monday); sunday.setDate(monday.getDate() + 6); sunday.setHours(23,59,59,999);
  return d >= monday && d <= sunday;
}

function isInLastWeek(d) {
  if (!d) return false;
  const now = new Date();
  const day = now.getDay();
  const diffToMon = (day + 6) % 7;
  const monday = new Date(now); monday.setDate(now.getDate() - diffToMon - 7); monday.setHours(0,0,0,0);
  const sunday = new Date(monday); sunday.setDate(monday.getDate() + 6); sunday.setHours(23,59,59,999);
  return d >= monday && d <= sunday;
}

function isInCurrentMonth(d) { 
  if (!d) return false;
  const n = new Date(); 
  return d.getFullYear()===n.getFullYear() && d.getMonth()===n.getMonth(); 
}

function isInLastMonth(d) { 
  if (!d) return false;
  const n = new Date(); 
  const lm = new Date(n.getFullYear(), n.getMonth()-1, 1); 
  return d.getFullYear()===lm.getFullYear() && d.getMonth()===lm.getMonth(); 
}

function isInCurrentYear(d) { 
  if (!d) return false;
  const n = new Date(); 
  return d.getFullYear()===n.getFullYear(); 
}
async function loadAndCompute() {
  try {
    console.log('[Customer Dashboard] Loading data...');
    const [incRes, expRes] = await Promise.all([
      window.axios.get('/pages/customer/income/list'),
      window.axios.get('/pages/customer/expense/list'),
    ]);
    const incomes = Array.isArray(incRes.data) ? incRes.data : [];
    const expenses = Array.isArray(expRes.data) ? expRes.data : [];

    console.log('[Customer Dashboard] Data loaded:', { incomes: incomes.length, expenses: expenses.length });

    // Check if we have encrypted data that needs decryption
    const hasEncryptedIncomes = incomes.some(r => r.salary === '[encrypted]' && r.salary_pgp);
    const hasEncryptedExpenses = expenses.some(r => r.price === '[encrypted]' && r.price_pgp);
    
    console.log('[Customer Dashboard] Encryption status:', { hasEncryptedIncomes, hasEncryptedExpenses });

    console.log('[Customer Dashboard] Sample income data:', incomes.slice(0, 2));
    console.log('[Customer Dashboard] Sample expense data:', expenses.slice(0, 2));
    
    // Jika ada data terenkripsi dan belum unlock, minta user unlock dulu
    if ((hasEncryptedIncomes || hasEncryptedExpenses) && !e2ee.isUnlocked?.value) {
      const ok = await ensureUnlocked();
      if (!ok) {
        console.warn('[Customer Dashboard] User did not unlock E2EE; keep server fallback values');
      }
    }

    // Debug date parsing
    if (incomes.length > 0) {
      const rawDate = incomes[0].date;
      const sampleDate = parseDate(rawDate);
      console.log('[Customer Dashboard] Sample income date:', rawDate, '→ parsed:', sampleDate);
      if (sampleDate) {
        console.log('[Customer Dashboard] Income date isCurrentMonth:', isInCurrentMonth(sampleDate));
      } else {
        console.warn('[Customer Dashboard] Income date could not be parsed!');
      }
    }
    if (expenses.length > 0) {
      const rawDate = expenses[0].purchase_date;
      const sampleDate = parseDate(rawDate);
      console.log('[Customer Dashboard] Sample expense date:', rawDate, '→ parsed:', sampleDate);
      if (sampleDate) {
        console.log('[Customer Dashboard] Expense date isCurrentMonth:', isInCurrentMonth(sampleDate));
      } else {
        console.warn('[Customer Dashboard] Expense date could not be parsed!');
      }
    }

    const decIncomes = await Promise.all(incomes.map(async (r, idx) => {
      const amount = await decryptAmount(r.salary, r.salary_pgp);
      if (idx < 2) console.log('[Customer Dashboard] Processed income:', { original: r.salary, decrypted: amount });
      return { date: r.date, amount };
    }));
    
    const decExpenses = await Promise.all(expenses.map(async (r, idx) => {
      const amount = await decryptAmount(r.price, r.price_pgp);
      if (idx < 2) console.log('[Customer Dashboard] Processed expense:', { original: r.price, decrypted: amount });
      return { date: r.purchase_date, amount };
    }));

    // Expenses aggregations
    const today = new Date(); today.setHours(0,0,0,0);
    let daily = 0, weekly = 0, monthly = 0, weeklyPrev = 0, dailyPrev = 0, monthlyPrev = 0;
    for (const e of decExpenses) {
      const d = parseDate(e.date); if (!d) continue;
      if (inSameDay(d, today)) daily += e.amount;
      const yesterday = new Date(today); yesterday.setDate(today.getDate()-1);
      if (inSameDay(d, yesterday)) dailyPrev += e.amount;
      if (isInCurrentWeek(d)) weekly += e.amount;
      if (isInLastWeek(d)) weeklyPrev += e.amount;
      if (isInCurrentMonth(d)) monthly += e.amount;
      if (isInLastMonth(d)) monthlyPrev += e.amount;
    }

    // Income aggregations
    let monthlyIncome = 0, monthlyIncomePrev = 0;
    for (const r of decIncomes) {
      const d = parseDate(r.date); if (!d) continue;
      if (isInCurrentMonth(d)) monthlyIncome += r.amount || 0;
      if (isInLastMonth(d)) monthlyIncomePrev += r.amount || 0;
    }

    // Set values
    pengeluaranHariIniX.value = daily;
    pengeluaranKemarinX.value = dailyPrev;
    dailyChangeX.value = dailyPrev > 0 ? ((daily - dailyPrev) / dailyPrev) * 100 : 0;

    pengeluaranMingguIniX.value = weekly;
    pengeluaranMingguLaluX.value = weeklyPrev;
    weeklyChangeX.value = weeklyPrev > 0 ? ((weekly - weeklyPrev) / weeklyPrev) * 100 : 0;

    pengeluaranBulanIniX.value = monthly;
    pengeluaranBulanLaluX.value = monthlyPrev;
    spendingChangeX.value = monthlyPrev > 0 ? ((monthly - monthlyPrev) / monthlyPrev) * 100 : 0;

    gajiBulanIniX.value = monthlyIncome;
    gajiBulanLaluX.value = monthlyIncomePrev;
    incomeChangeX.value = monthlyIncomePrev > 0 ? ((monthlyIncome - monthlyIncomePrev) / monthlyIncomePrev) * 100 : 0;

    // Net balance should reflect lifetime by default (not only this month)
    const lifetimeIncome = decIncomes.reduce((s, r) => s + (Number(r.amount) || 0), 0);
    const lifetimeExpense = decExpenses.reduce((s, r) => s + (Number(r.amount) || 0), 0);
    totalSaldoX.value = lifetimeIncome - lifetimeExpense;
    const prevNet = monthlyIncomePrev - monthlyPrev;
    balanceChangeX.value = prevNet > 0 ? ((totalSaldoX.value - prevNet) / prevNet) * 100 : 0;

    // Chart monthly for current year
    const monthlyTotals = Array(12).fill(0);
    for (const e of decExpenses) {
      const d = parseDate(e.date); if (!d) continue;
      if (isInCurrentYear(d)) monthlyTotals[d.getMonth()] += e.amount || 0;
    }
    chartMonthly.value = monthlyTotals.map((total, idx) => ({ month: idx + 1, total }));
    
    console.log('[Customer Dashboard] Final computed values:', {
      daily, weekly, monthly, monthlyIncome, totalBalance: totalSaldoX.value,
      dailyChange: dailyChangeX.value, weeklyChange: weeklyChangeX.value, 
      spendingChange: spendingChangeX.value, incomeChange: incomeChangeX.value
    });
  } catch (err) { 
    console.error('[Customer Dashboard] Failed to load stats:', err);
    // Show error to user if needed
    if (window.Swal && err.message.includes('locked')) {
      window.Swal.fire({
        title: 'E2EE Locked',
        text: 'Your encryption keys are not available. Please refresh and unlock.',
        icon: 'warning'
      });
    }
  }
}

onMounted(async () => {
  console.log('[Customer Dashboard] Component mounted, initializing...');
  
  try { 
    // First, ensure E2EE store is fully initialized
    await e2ee.initialize();
    console.log('[Customer Dashboard] E2EE store initialized');
    console.log('[Customer Dashboard] E2EE enabled:', e2ee.e2eeEnabled?.value);
    console.log('[Customer Dashboard] E2EE unlocked:', e2ee.isUnlocked?.value);
  } catch (error) {
    console.error('[Customer Dashboard] Failed to initialize E2EE:', error);
  }
  
  await loadAndCompute();
});

</script>
