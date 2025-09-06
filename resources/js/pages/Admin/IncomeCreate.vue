<template>
  <MainLayout title="Add Income - Admin" page-title="Add Income" :requireUnlock="true">
    <div class="card bg-white border-0 rounded-3 mb-4">
      <div class="card-body p-4">
        <h3 class="mb-4">Add Income</h3>
        <form @submit.prevent="onSubmit" class="row g-3">
          <div class="col-12 col-md-6 col-lg-4">
            <label class="form-label">Category</label>
            <template v-if="!catLoading">
              <select v-model="form.category_incomes_uuid" class="form-select" required>
                <option value="" disabled>Select category</option>
                <option v-for="c in categories" :key="c.uuid" :value="c.uuid">{{ c.name }}</option>
              </select>
            </template>
            <template v-else>
              <select class="form-select" disabled>
                <option>Loading categories...</option>
              </select>
            </template>
          </div>
          <div class="col-12 col-md-6 col-lg-4">
            <label class="form-label">Salary</label>
            <input type="text" class="form-control" :value="formatRupiah(salaryRaw)" @input="onSalaryInput($event.target.value)" placeholder="e.g. 1.500.000" required />
          </div>
          <div class="col-12 col-md-6 col-lg-4">
            <label class="form-label">Date</label>
            <input type="date" class="form-control" v-model="form.date" required />
          </div>
          <div class="col-12">
            <label class="form-label">Description</label>
            <textarea class="form-control" v-model="form.description" rows="4" placeholder="Description" required></textarea>
          </div>
          <div class="col-12 d-flex gap-2">
            <Link href="/pages/admin/income" class="btn btn-light">Cancel</Link>
            <button type="submit" class="btn btn-primary" :disabled="saving">{{ saving ? 'Saving...' : 'Save' }}</button>
          </div>
        </form>
      </div>
    </div>
  </MainLayout>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';
import MainLayout from '../../layouts/MainLayout.vue';
import { useE2EE } from '../../stores/e2ee.js';
import { Link, router } from '@inertiajs/vue3';

const e2ee = useE2EE();
const form = ref({ category_incomes_uuid: '', date: '', description: '' });
const salaryRaw = ref('');
const saving = ref(false);
const categories = ref([]);
const catLoading = ref(true);

async function encryptForSelf(plain){
  const keys = e2ee.userKeys?.value || null; if (!keys?.pgp_public_key) throw new Error('No pub');
  const pub = await window.openpgp.readKey({ armoredKey: keys.pgp_public_key });
  const msg = await window.openpgp.createMessage({ text: plain });
  return await window.openpgp.encrypt({ message: msg, encryptionKeys: pub });
}
async function getDecPriv(){
  if (getDecPriv.cache) return getDecPriv.cache;
  const keys = e2ee.userKeys?.value || null; if (!keys?.pgp_private_key_armor) throw new Error('No priv');
  let R = e2ee.Rb64?.value || null; try { if (!R) R = sessionStorage.getItem('e2ee_R_b64') || null; } catch {}
  if (!R) throw new Error('Locked');
  const priv = await window.openpgp.readPrivateKey({ armoredKey: keys.pgp_private_key_armor });
  const dec = await window.openpgp.decryptKey({ privateKey: priv, passphrase: R });
  getDecPriv.cache = dec; return dec;
}
async function decryptArmor(armored){ const msg = await window.openpgp.readMessage({ armoredMessage: armored }); const priv = await getDecPriv(); const { data } = await window.openpgp.decrypt({ message: msg, decryptionKeys: priv }); return data; }

async function fetchCategories(){
  try {
    catLoading.value = true;
    const res = await window.axios.get('/pages/admin/category/income/list');
    const raw = res.data || [];
    const out = [];
    for (const r of raw) {
      let name = r.name_category_incomes || '';
      if (name === '[encrypted]' && r.name_category_incomes_pgp) { try { name = await decryptArmor(r.name_category_incomes_pgp); } catch {} }
      out.push({ uuid: r.uuid, name });
    }
    categories.value = out;
  } finally { catLoading.value = false; }
}

function toDigits(v){ return String(v || '').replace(/[^\d]/g,''); }
function formatRupiah(v){ const s = toDigits(v); if (!s) return ''; return 'Rp. ' + s.replace(/\B(?=(\d{3})+(?!\d))/g, '.'); }
function onSalaryInput(val){ salaryRaw.value = toDigits(val); }

async function onSubmit(){
  saving.value = true;
  try {
    const fd = new FormData();
    fd.append('category_incomes_uuid', form.value.category_incomes_uuid);
    let armorSalary = null; try { armorSalary = await encryptForSelf(String(salaryRaw.value)); } catch {}
    if (armorSalary) { fd.append('salary', '[encrypted]'); fd.append('salary_pgp', armorSalary); }
    else { fd.append('salary', String(salaryRaw.value)); }
    fd.append('date', form.value.date);
    let armor = null; try { armor = await encryptForSelf(form.value.description); } catch {}
    if (armor) { fd.append('description', '[encrypted]'); fd.append('description_pgp', armor); }
    else { fd.append('description', form.value.description); }
    await window.axios.post('/pages/admin/income/store', fd);
    window.showCustomAlert?.('success', 'Saved');
    router.visit('/pages/admin/income');
  } catch (e) {
    const msg = e?.response?.data?.message || 'Failed to save';
    window.showCustomAlert?.('danger', msg);
  } finally { saving.value = false; }
}

onMounted(async () => {
  try { await e2ee.fetchUserKeys(); } catch {}
  await fetchCategories();
});

watch(() => e2ee.isUnlocked?.value, (v) => { if (v) fetchCategories(); });
</script>

