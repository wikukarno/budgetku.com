<template>
  <MainLayout title="Edit Expense - Admin" page-title="Edit Expense" :requireUnlock="true">
    <div class="card bg-white border-0 rounded-3 mb-4">
      <div class="card-body p-4">
        <h3 class="mb-4">Edit Expense</h3>
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
            <input type="text" class="form-control" v-model="form.name_item" required />
          </div>
          <div class="col-12 col-md-6">
            <label class="form-label">Date</label>
            <input type="date" class="form-control" v-model="form.purchase_date" required />
          </div>

          <div class="col-12 col-md-6">
            <label class="form-label">Price</label>
            <input v-if="!loadingPrice" type="text" class="form-control" :value="formatRupiah(priceRaw)" @input="onPriceInput($event.target.value)" required />
            <input v-else type="text" class="form-control" placeholder="Decrypting..." disabled />
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
            <div class="d-flex justify-content-between align-items-center">
              <label class="form-label mb-0">Proof of Payment (max 2MB)</label>
            </div>
            <input type="file" class="form-control mt-2" @change="onFile" />
            <div v-if="proofPath" class="mt-3">
              <div v-if="previewLoading" class="text-muted small d-flex align-items-center gap-2"><span class="spinner-border spinner-border-sm"></span> <span>Decrypting preview...</span></div>
              <div v-if="previewError" class="text-danger small">{{ previewError }}</div>

              <div v-if="previewUrl" class="border rounded-3 shadow-sm overflow-hidden">
                <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom bg-light">
                  <div class="small text-truncate d-flex align-items-center gap-2">
                    <i :data-feather="previewMime.startsWith('image/') ? 'image' : 'file'"></i>
                    <span class="text-muted">{{ displayFileName() }}</span>
                  </div>
                  <button type="button" class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-1" @click="downloadDecrypted">
                    <i data-feather="download"></i>
                    <span class="d-none d-md-inline">Download</span>
                  </button>
                </div>
                <div class="p-2 text-center bg-white">
                  <template v-if="previewMime.startsWith('image/')">
                    <img :src="previewUrl" alt="Preview" class="img-fluid rounded-2" style="max-height: 360px; object-fit: contain;" />
                  </template>
                  <template v-else>
                    <div class="py-4 text-muted small">File siap diunduh.</div>
                  </template>
                </div>
              </div>
            </div>
          </div>

          <div class="col-12 d-flex gap-2 justify-content-end">
            <Link href="/pages/admin/expense" class="btn btn-light">Cancel</Link>
            <button type="submit" class="btn btn-primary" :disabled="saving">{{ saving ? 'Saving...' : 'Update' }}</button>
          </div>
        </form>
      </div>
    </div>
  </MainLayout>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount, watch, nextTick } from 'vue';
import { usePage, Link, router } from '@inertiajs/vue3';
import MainLayout from '../../layouts/MainLayout.vue';
import { useE2EE } from '../../stores/e2ee.js';

const e2ee = useE2EE();
const page = usePage();
const uuid = page.props.uuid;

const form = ref({ category_finances_uuid: '', name_item: '', purchase_date: '', payment_methods_uuid: '' });
const priceRaw = ref('');
const buktiFile = ref(null);
const proofPath = ref('');
const previewUrl = ref('');
const previewMime = ref('');
const previewLoading = ref(false);
const previewError = ref('');
const categories = ref([]);
const paymentMethods = ref([]);
const catLoading = ref(true);
const pmLoading = ref(true);
const loadingPrice = ref(true);
const saving = ref(false);

function toDigits(v){ return String(v || '').replace(/[^\d]/g,''); }
function formatRupiah(v){ const s = toDigits(v); if (!s) return ''; return 'Rp. ' + s.replace(/\B(?=(\d{3})+(?!\d))/g, '.'); }
function onPriceInput(val){ priceRaw.value = toDigits(val); }
function onFile(e){ const f = e.target.files?.[0] || null; buktiFile.value = f; }

async function getPriv(){
  if (getPriv.cache) return getPriv.cache;
  const keys = e2ee.userKeys?.value || null; if (!keys?.pgp_private_key_armor) throw new Error('No key');
  let R = e2ee.Rb64?.value || null; try { if (!R) R = sessionStorage.getItem('e2ee_R_b64') || null; } catch{}
  if (!R) throw new Error('Locked');
  const priv = await window.openpgp.readPrivateKey({ armoredKey: keys.pgp_private_key_armor });
  const dec = await window.openpgp.decryptKey({ privateKey: priv, passphrase: R });
  getPriv.cache = dec; return dec;
}
async function decryptArmor(armor){
  const msg = await window.openpgp.readMessage({ armoredMessage: armor });
  const priv = await getPriv();
  const { data } = await window.openpgp.decrypt({ message: msg, decryptionKeys: priv });
  return data;
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
  try { catLoading.value = true; const res = await window.axios.get('/pages/admin/category/expense/list'); const raw = res.data || []; const out=[]; for (const r of raw){ let name=r.name_category_finances||''; if (name==='[encrypted]' && r.name_category_finances_pgp){ try { const priv=await getPriv(); const m=await window.openpgp.readMessage({ armoredMessage:r.name_category_finances_pgp }); const { data }=await window.openpgp.decrypt({ message:m, decryptionKeys:priv }); name=data; } catch{} } out.push({ uuid:r.uuid, name }); } categories.value = out; } finally { catLoading.value = false; } }
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
          const priv = await getPriv();
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

async function fetchData(){
  const { data } = await window.axios.get('/pages/admin/expense/show', { params: { uuid } });
  form.value.category_finances_uuid = data.category_finances_uuid;
  // name_item may be encrypted
  try {
    if (data.name_item === '[encrypted]' && data.name_item_pgp) {
      const txt = await decryptArmor(data.name_item_pgp);
      form.value.name_item = txt;
    } else {
      form.value.name_item = data.name_item || '';
    }
  } catch { form.value.name_item = ''; }
  form.value.purchase_date = (data.purchase_date || '').slice(0,10);
  form.value.payment_methods_uuid = data.payment_methods_uuid;
  proofPath.value = data.bukti_pembayaran || '';
  // price may be encrypted
  try {
    if (data.price === '[encrypted]' && data.price_pgp) {
      const txt = await decryptArmor(data.price_pgp);
      priceRaw.value = toDigits(txt);
    } else {
      priceRaw.value = toDigits(String(data.price || ''));
    }
  } catch { priceRaw.value = ''; }
  loadingPrice.value = false;
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
      } catch { fd.append('bukti_pembayaran', buktiFile.value); }
    }
    let armorPrice = null; try { armorPrice = await encryptForSelf(String(priceRaw.value)); } catch {}
    if (armorPrice) { fd.append('price','[encrypted]'); fd.append('price_pgp', armorPrice); }
    else { fd.append('price', String(priceRaw.value)); }
    await window.axios.post(`/pages/admin/expense/update/${uuid}?_method=PUT`, fd);
    window.showCustomAlert?.('success','Updated');
    router.visit('/pages/admin/expense');
  } catch (e) {
    window.showCustomAlert?.('danger', e?.response?.data?.message || 'Failed to update');
  } finally { saving.value = false; }
}

onMounted(async () => { 
  try { await e2ee.fetchUserKeys(); } catch{}
  await fetchCategories();
  await fetchPaymentMethods();
  await fetchData();
  // Auto preview if file exists
  if (proofPath.value) { try { await loadProofPreview(); } catch {} }
});

function detectMime(bytes){
  if (!bytes || bytes.length < 12) return 'application/octet-stream';
  // JPEG
  if (bytes[0] === 0xFF && bytes[1] === 0xD8 && bytes[2] === 0xFF) return 'image/jpeg';
  // PNG
  if (bytes[0] === 0x89 && bytes[1] === 0x50 && bytes[2] === 0x4E && bytes[3] === 0x47) return 'image/png';
  // WEBP: RIFF....WEBP
  if (bytes[0] === 0x52 && bytes[1] === 0x49 && bytes[2] === 0x46 && bytes[3] === 0x46 && bytes[8] === 0x57 && bytes[9] === 0x45 && bytes[10] === 0x42 && bytes[11] === 0x50) return 'image/webp';
  return 'application/octet-stream';
}

async function decryptArmorBinary(armored){
  const msg = await window.openpgp.readMessage({ armoredMessage: armored });
  const priv = await getPriv();
  const res = await window.openpgp.decrypt({ message: msg, decryptionKeys: priv, format: 'binary' });
  return res.data; // Uint8Array
}

async function loadProofPreview(){
  if (!proofPath.value) return;
  previewLoading.value = true; previewError.value = '';
  try {
    const url = `/storage/${proofPath.value}`;
    const resp = await fetch(url);
    if (!resp.ok) throw new Error('Failed to fetch file');
    const armored = await resp.text();
    const bytes = await decryptArmorBinary(armored);
    const mime = detectMime(bytes);
    const blob = new Blob([bytes], { type: mime });
    if (previewUrl.value) { try { URL.revokeObjectURL(previewUrl.value); } catch {} }
    previewMime.value = mime;
    previewUrl.value = URL.createObjectURL(blob);
    await nextTick(); try { window.feather?.replace?.(); } catch {}
  } catch (e) {
    previewError.value = 'Gagal memuat preview';
  } finally { previewLoading.value = false; }
}

function extFromMime(mime){
  if (mime === 'image/jpeg') return 'jpg';
  if (mime === 'image/png') return 'png';
  if (mime === 'image/webp') return 'webp';
  return 'bin';
}

async function downloadDecrypted(){
  try {
    if (!previewUrl.value) {
      await loadProofPreview();
      if (!previewUrl.value) throw new Error('No preview');
    }
    const a = document.createElement('a');
    a.href = previewUrl.value;
    const base = (proofPath.value || 'proof').split('/').pop()?.replace(/\.pgp$/i,'') || 'proof';
    a.download = `${base}.${extFromMime(previewMime.value || 'application/octet-stream')}`;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
  } catch (e) {
    previewError.value = 'Gagal mengunduh file terdekripsi';
  }
}

onBeforeUnmount(() => { if (previewUrl.value) { try { URL.revokeObjectURL(previewUrl.value); } catch {} } });

// Auto-try preview again when E2EE unlocked
watch(() => e2ee.isUnlocked?.value, async (v) => {
  if (v && proofPath.value && !previewUrl.value && !previewLoading.value) {
    try { await loadProofPreview(); } catch {}
  }
});

function displayFileName(){
  const base = (proofPath.value || '').split('/').pop() || '';
  return base.replace(/\.pgp$/i, '') || 'proof';
}
</script>
