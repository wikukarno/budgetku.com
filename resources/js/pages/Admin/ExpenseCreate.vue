<template>
  <MainLayout title="Add Expense - Admin" page-title="Add Expense" :requireUnlock="true">
    <div class="card bg-white border-0 rounded-3 mb-4">
      <div class="card-body p-4">
        <h3 class="mb-4">Add Expense</h3>
        <form @submit.prevent="onSubmit" class="row g-3">
          <div class="col-12 col-md-6">
            <label class="form-label">Category</label>
            <template v-if="!catLoading">
              <select v-model="form.category_finances_uuid" class="form-select" required>
                <option value="" disabled>Select</option>
                <option v-for="c in categories" :key="c.uuid" :value="c.uuid">{{ c.name }}</option>
              </select>
            </template>
            <template v-else>
              <select class="form-select" disabled><option>Loading categories...</option></select>
            </template>
          </div>
          <div class="col-12 col-md-6">
            <label class="form-label">Name</label>
            <input type="text" class="form-control" v-model="form.name_item" placeholder="Name of item" required />
          </div>
          <div class="col-12 col-md-6">
            <label class="form-label">Date</label>
            <input type="date" class="form-control" v-model="form.purchase_date" required />
          </div>

          <div class="col-12 col-md-6">
            <label class="form-label">Price</label>
            <input type="text" class="form-control" :value="formatRupiah(priceRaw)" @input="onPriceInput($event.target.value)" placeholder="e.g. 1.500.000" required />
          </div>
          <div class="col-12 col-md-6">
            <label class="form-label">Payment Method</label>
            <template v-if="!pmLoading">
              <select v-model="form.payment_methods_uuid" class="form-select" required>
                <option value="" disabled>Select</option>
                <option v-for="p in paymentMethods" :key="p.uuid" :value="p.uuid">{{ p.name }}</option>
              </select>
            </template>
            <template v-else>
              <select class="form-select" disabled><option>Loading payment methods...</option></select>
            </template>
          </div>
          <div class="col-12 col-md-6">
            <label class="form-label">Proof of Payment (max 2MB)</label>
            <input type="file" class="form-control" @change="onFile" />
          </div>

          <div class="col-12 d-flex gap-2 justify-content-end">
            <Link href="/pages/admin/expense" class="btn btn-light">Cancel</Link>
            <button type="submit" class="btn btn-primary" :disabled="saving">{{ saving ? 'Saving...' : 'Save' }}</button>
          </div>
        </form>
      </div>
    </div>
  </MainLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import MainLayout from '../../layouts/MainLayout.vue';
import { useE2EE } from '../../stores/e2ee.js';

const e2ee = useE2EE();
const form = ref({ category_finances_uuid: '', name_item: '', purchase_date: '', payment_methods_uuid: '' });
const priceRaw = ref('');
const buktiFile = ref(null);
const saving = ref(false);
const categories = ref([]);
const paymentMethods = ref([]);
const catLoading = ref(true);
const pmLoading = ref(true);

function showPreloader(){ try { const el = document.querySelector('.preloader'); if (el) el.style.display = 'block'; } catch {} }
function hidePreloader(){ try { const el = document.querySelector('.preloader'); if (el) el.style.display = 'none'; } catch {} }

function toDigits(v){ return String(v || '').replace(/[^\d]/g,''); }
function formatRupiah(v){ const s = toDigits(v); if (!s) return ''; return 'Rp. ' + s.replace(/\B(?=(\d{3})+(?!\d))/g, '.'); }
function onPriceInput(val){ priceRaw.value = toDigits(val); }
function onFile(e){ const f = e.target.files?.[0] || null; buktiFile.value = f; }

async function getDecPriv(){
  if (getDecPriv.cache) return getDecPriv.cache;
  const keys = e2ee.userKeys?.value || null; if (!keys?.pgp_private_key_armor) throw new Error('No key');
  let R = e2ee.Rb64?.value || null; try { if (!R) R = sessionStorage.getItem('e2ee_R_b64') || null; } catch{}
  if (!R) throw new Error('Locked');
  const priv = await window.openpgp.readPrivateKey({ armoredKey: keys.pgp_private_key_armor });
  const dec = await window.openpgp.decryptKey({ privateKey: priv, passphrase: R });
  getDecPriv.cache = dec; return dec;
}
async function encryptForSelf(plain){
  const keys = e2ee.userKeys?.value || null; if (!keys?.pgp_public_key) throw new Error('No pub');
  const pub = await window.openpgp.readKey({ armoredKey: keys.pgp_public_key });
  const msg = await window.openpgp.createMessage({ text: plain });
  return await window.openpgp.encrypt({ message: msg, encryptionKeys: pub });
}
async function encryptBinaryForSelf(bytes){
  const keys = e2ee.userKeys?.value || null; if (!keys?.pgp_public_key) throw new Error('No pub');
  const pub = await window.openpgp.readKey({ armoredKey: keys.pgp_public_key });
  const msg = await window.openpgp.createMessage({ binary: new Uint8Array(bytes) });
  return await window.openpgp.encrypt({ message: msg, encryptionKeys: pub });
}

async function fetchCategories(){
  try { catLoading.value = true; const res = await window.axios.get('/pages/admin/category/expense/list'); const raw = res.data || []; const out=[]; for (const r of raw){ let name=r.name_category_finances||''; if (name==='[encrypted]' && r.name_category_finances_pgp){ try { const priv=await getDecPriv(); const m=await window.openpgp.readMessage({ armoredMessage:r.name_category_finances_pgp }); const { data }=await window.openpgp.decrypt({ message:m, decryptionKeys:priv }); name=data; } catch{} } out.push({ uuid:r.uuid, name }); } categories.value = out; } finally { catLoading.value = false; } }
async function fetchPaymentMethods(){
  try {
    pmLoading.value = true;
    const res = await window.axios.get('/pages/admin/payment-method/list');
    const raw = res.data || [];
    const out = [];
    for (const r of raw) {
      let name = r.name || '';
      if (name === '[encrypted]' && r.name_pgp) {
        try {
          const priv = await getDecPriv();
          const msg = await window.openpgp.readMessage({ armoredMessage: r.name_pgp });
          const { data } = await window.openpgp.decrypt({ message: msg, decryptionKeys: priv });
          name = data;
        } catch {}
      }
      out.push({ uuid: r.uuid, name });
    }
    paymentMethods.value = out;
  } finally { pmLoading.value = false; }
}

async function onSubmit(){
  saving.value = true;
  try {
    const fd = new FormData();
    fd.append('category_finances_uuid', form.value.category_finances_uuid);
    // Encrypt name_item when possible
    let nameArmor = null; try { nameArmor = await encryptForSelf(form.value.name_item || ''); } catch {}
    if (nameArmor) { fd.append('name_item','[encrypted]'); fd.append('name_item_pgp', nameArmor); }
    else { fd.append('name_item', form.value.name_item || ''); }
    fd.append('purchase_date', form.value.purchase_date);
    fd.append('payment_methods_uuid', form.value.payment_methods_uuid);
    if (buktiFile.value) {
      try {
        const buf = await buktiFile.value.arrayBuffer();
        const armor = await encryptBinaryForSelf(buf);
        const blob = new Blob([armor], { type: 'application/pgp-encrypted' });
        const filename = (buktiFile.value.name || 'proof') + '.pgp';
        const encFile = new File([blob], filename, { type: 'application/pgp-encrypted' });
        fd.append('bukti_pembayaran', encFile);
      } catch {
        // fallback to raw file if encryption failed
        fd.append('bukti_pembayaran', buktiFile.value);
      }
    }
    let armorPrice = null; try { armorPrice = await encryptForSelf(String(priceRaw.value)); } catch {}
    if (armorPrice) { fd.append('price','[encrypted]'); fd.append('price_pgp', armorPrice); }
    else { fd.append('price', String(priceRaw.value)); }
    showPreloader();
    await window.axios.post('/pages/admin/expense/store', fd);
    window.showCustomAlert?.('success','Saved');
    router.visit('/pages/admin/expense');
  } catch (e) {
    window.showCustomAlert?.('danger', e?.response?.data?.message || 'Failed to save');
  } finally { saving.value = false; hidePreloader(); }
}

onMounted(async () => { try { await e2ee.fetchUserKeys(); } catch{} await fetchCategories(); await fetchPaymentMethods(); });
</script>
