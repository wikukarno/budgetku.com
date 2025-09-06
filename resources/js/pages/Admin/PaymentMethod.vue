<template>
  <MainLayout title="Payment Methods - Admin" page-title="Payment Methods">
    <div class="card bg-white border-0 rounded-3 mb-4">
      <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
          <h3 class="mb-0">Payment Methods</h3>
          <button class="btn btn-primary" type="button" @click="openCreate">
            <i data-feather="plus" class="me-2"></i>
            Add New
          </button>
        </div>

        <div class="default-table-area all-products">
          <div class="table-responsive">
            <table class="table align-middle" id="pmTable">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Name</th>
                  <th>Created At</th>
                  <th>Updated At</th>
                  <th>Action</th>
                </tr>
              </thead>
            </table>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="pmModal" tabindex="-1" aria-labelledby="pmModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h1 class="modal-title fs-5" id="pmModalLabel"></h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form @submit.prevent="onSubmit">
            <div class="modal-body">
              <input type="hidden" v-model="form.uuid" />
              <div class="form-group">
                <label for="pm_name">Name</label>
                <input type="text" id="pm_name" class="form-control" placeholder="e.g. Cash, Bank Transfer" v-model="form.name" required />
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-danger text-white" data-bs-dismiss="modal">Cancel</button>
              <button type="submit" class="btn btn-primary">{{ saving ? 'Saving...' : (form.uuid ? 'Update' : 'Save') }}</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </MainLayout>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';
import MainLayout from '../../layouts/MainLayout.vue';
import { useE2EE } from '../../stores/e2ee.js';

const form = ref({ uuid: '', name: '' });
const saving = ref(false);
const e2ee = useE2EE();

function openCreate(){
  form.value = { uuid: '', name: '' };
  document.getElementById('pmModalLabel').innerText = 'Add New Payment Method';
  const modal = new window.bootstrap.Modal(document.getElementById('pmModal'));
  modal.show();
}

async function btnEditPaymentMethod(uuid){
  form.value = { uuid: '', name: '' };
  document.getElementById('pmModalLabel').innerText = 'Edit Payment Method';
  const modal = new window.bootstrap.Modal(document.getElementById('pmModal'));
  modal.show();
  try {
    const res = await window.axios.get('/pages/admin/payment-method/show', { params: { uuid } });
    if (res?.data?.status && res?.data?.data) {
      const d = res.data.data;
      form.value.uuid = d.uuid;
      if (d.name === '[encrypted]' && d.name_pgp) {
        try { form.value.name = await decryptArmor(d.name_pgp); } catch { form.value.name = ''; }
      } else {
        form.value.name = d.name || '';
      }
    }
  } catch {}
}

async function btnDeletePaymentMethod(uuid){
  window.Swal.fire({
    title: 'Are you sure?', text: 'Data will be deleted!', icon: 'warning',
    showCancelButton: true, confirmButtonColor: '#3085d6', cancelButtonColor: '#d33',
    confirmButtonText: 'Yes', cancelButtonText: 'Cancel'
  }).then((result) => {
    if (!result.isConfirmed) return;
    (async () => {
      try {
        await window.axios.delete('/pages/admin/payment-method/delete', { data: { uuid } });
        window.$('#pmTable').DataTable().ajax.reload();
        window.showCustomAlert?.('success', 'Deleted');
      } catch (e) {
        const msg = e?.response?.data?.message || 'Failed to delete';
        window.showCustomAlert?.('danger', msg);
      }
    })();
  });
}

async function onSubmit(){
  saving.value = true;
  try {
    const fd = new FormData();
    if (form.value.uuid) fd.append('uuid', form.value.uuid);
    fd.append('_token', document.querySelector('meta[name="csrf-token"]').content);
    // Encrypt name if possible
    let armor = null;
    try { armor = await encryptForSelf(form.value.name); } catch { armor = null; }
    if (armor) {
      fd.append('name', '[encrypted]');
      fd.append('name_pgp', armor);
    } else {
      fd.append('name', form.value.name);
    }
    await window.axios.post('/pages/admin/payment-method/store', fd);
    window.bootstrap.Modal.getInstance(document.getElementById('pmModal'))?.hide();
    window.$('#pmTable').DataTable().ajax.reload();
    window.showCustomAlert?.('success', 'Saved');
  } catch (e) {
    window.showCustomAlert?.('danger', 'Failed to save');
  } finally { saving.value = false; }
}

onMounted(async () => {
  // Initialize E2EE store first to avoid race conditions
  try {
    await e2ee.initialize();
  } catch (error) {
    console.warn('[Admin PaymentMethod] Failed to initialize E2EE:', error);
  }
  
  const $ = window.$; if (!$) return;
  const currentUrl = window.location.pathname;
  
  // Destroy existing DataTable if it exists
  if ($.fn.DataTable.isDataTable('#pmTable')) {
    $('#pmTable').DataTable().destroy();
  }
  
  $('#pmTable').DataTable({
    processing: true,
    serverSide: false,
    ajax: { url: currentUrl + '/list', dataSrc: '' },
    columns: [
      { data: null, name: 'no', className: 'text-center', render: function (data, type, row, meta) {
          const start = (typeof meta?.settings?._iDisplayStart === 'number') ? meta.settings._iDisplayStart : (parseInt(meta?.settings?._iDisplayStart, 10) || 0);
          return Number(meta.row) + start + 1; } },
      { data: 'name', name: 'name', render: function (data, type, row) {
          if (type === 'filter' || type === 'sort') return row?.name || '';
          try {
            if (row && row.name === '[encrypted]' && row.name_pgp) {
              const pgp = encodeURIComponent(row.name_pgp);
              return `<span class="enc-name" data-pgp="${pgp}"><span class="spinner-border spinner-border-sm me-1"></span>Decrypting...</span>`;
            }
          } catch {}
          return data ?? '';
        } },
      { data: 'created_at', name: 'created_at' },
      { data: 'updated_at', name: 'updated_at' },
      { data: 'action', searchable: false, sortable: false }
    ],
    language: {
      search: '', searchPlaceholder: 'Search...', zeroRecords: 'No data available!',
      info: 'Showing _START_ to _END_ of _TOTAL_ entries', lengthMenu: 'Show _MENU_ entries',
      paginate: { previous: 'Previous', next: 'Next' }
    },
    columnDefs: [ { targets: 0, className: 'text-center', width: '60px', orderable: false }, { targets: 4, className: 'text-end', orderable: false } ],
    drawCallback: async function(){ try { window.feather?.replace?.(); } catch {} try { await decryptVisibleRows(); } catch {} },
    initComplete: function () {
      const lengthEl = $('.dataTables_length');
      const filterEl = $('.dataTables_filter');
      const header = $('<div class="row g-2 align-items-center mb-3 dt-header"></div>');
      const left = $('<div class="col-md-6"></div>').append(lengthEl);
      const right = $('<div class="col-md-6 text-md-end"></div>').append(filterEl);
      header.append(left).append(right);
      const wrapper = $('#pmTable').closest('.dataTables_wrapper');
      header.insertBefore(wrapper.find('.row').first());
    }
  });

  // Expose handlers for action buttons generated by server HTML
  window.btnEditPaymentMethod = btnEditPaymentMethod;
  window.btnDeletePaymentMethod = btnDeletePaymentMethod;
});

// E2EE helpers
let decryptedPrivateKey = null;
async function getDecryptedPrivateKey() {
  if (decryptedPrivateKey) return decryptedPrivateKey;
  
  // Ensure E2EE store is initialized before proceeding
  await e2ee.fetchUserKeys();
  const keys = e2ee.userKeys?.value || null;
  if (!keys || !keys.pgp_private_key_armor) throw new Error('No private key');
  
  let R = e2ee.Rb64?.value || null;
  try { if (!R) R = sessionStorage.getItem('e2ee_R_b64') || null; } catch {}
  if (!R) throw new Error('E2EE is locked');
  
  const priv = await window.openpgp.readPrivateKey({ armoredKey: keys.pgp_private_key_armor });
  decryptedPrivateKey = await window.openpgp.decryptKey({ privateKey: priv, passphrase: R });
  return decryptedPrivateKey;
}
async function decryptArmor(armoredMessage) {
  const message = await window.openpgp.readMessage({ armoredMessage });
  const privateKey = await getDecryptedPrivateKey();
  const { data } = await window.openpgp.decrypt({ message, decryptionKeys: privateKey });
  return data;
}
async function encryptForSelf(plain) {
  const keys = e2ee.userKeys?.value || null;
  if (!keys || !keys.pgp_public_key) throw new Error('No public key');
  const publicKey = await window.openpgp.readKey({ armoredKey: keys.pgp_public_key });
  const message = await window.openpgp.createMessage({ text: plain });
  const armor = await window.openpgp.encrypt({ message, encryptionKeys: publicKey });
  return armor;
}
async function decryptVisibleRows() {
  const $ = window.$;
  const api = $('#pmTable').DataTable();
  const rowsApi = api.rows({ page: 'current' });
  rowsApi.every(function(){
    const tr = this.node();
    const nameTd = $('td', tr).eq(1);
    const enc = nameTd.find('.enc-name');
    if (!enc.length || enc.data('dec') === 1) return;
    const armored = enc.attr('data-pgp');
    if (!armored) return;
    decryptArmor(decodeURIComponent(armored))
      .then(txt => { nameTd.text(txt); enc.data('dec', 1); })
      .catch(() => {});
  });
}
async function decryptAllRows() {
  const $ = window.$; const api = $('#pmTable').DataTable();
  const rows = api.rows().indexes();
  for (let i = 0; i < rows.length; i++) {
    const idx = rows[i]; const d = api.row(idx).data();
    if (d && d.name === '[encrypted]' && d.name_pgp) {
      try { const txt = await decryptArmor(d.name_pgp); d.name = txt; api.row(idx).data(d); } catch {}
    }
  }
  api.draw(false);
}
// Decrypt all rows after Ajax load so search works on plaintext
onMounted(() => {
  try {
    const $ = window.$; if (!$) return;
    $('#pmTable').on('xhr.dt', async function(){ try { await decryptAllRows(); } catch {} });
  } catch {}
});

watch(() => e2ee.isUnlocked?.value, (v) => { if (v) setTimeout(() => { try { decryptAllRows(); } catch {} }, 0); });
</script>
