<template>
  <MainLayout title="Income Category - Admin" page-title="Income Category" :requireUnlock="true">
    <div class="card bg-white border-0 rounded-3 mb-4">
      <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
          <h3 class="mb-0">Income Category</h3>
          <button class="btn btn-primary" type="button" @click="addCategoryIncome">
            <i data-feather="plus" class="me-2"></i>
            Add New
          </button>
        </div>

        <div class="default-table-area all-products">
          <div class="table-responsive">
            <table class="table align-middle" id="categoryIncomeTable">
              <thead>
                <tr>
                  <th>Number</th>
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
    <div class="modal fade" id="categoryIncomeModal" tabindex="-1" aria-labelledby="categoryIncomeModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h1 class="modal-title fs-5" id="categoryIncomeModalLabel"></h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form id="form-tambah-kategori-income" @submit.prevent="onSubmit">
            <div class="modal-body">
              <input type="hidden" name="id_category_income" id="id_category_income" v-model="form.id" />
              <div class="form-group">
                <label for="name_category_incomes">Name</label>
                <input type="text" name="name_category_incomes" id="name_category_incomes" class="form-control" placeholder="Salary or other" v-model="form.name" required />
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-danger text-white" data-bs-dismiss="modal">Cancel</button>
              <button type="submit" id="btnSaveKategoriKeuangan" class="btn btn-primary">{{ saving ? 'Saving...' : (form.id ? 'Update' : 'Save') }}</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </MainLayout>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';
import { useE2EE } from '../../stores/e2ee.js';
import MainLayout from '../../layouts/MainLayout.vue';

const form = ref({ uuid: '', legacyId: '', name: '' });
const saving = ref(false);
let dt = null;

function addCategoryIncome() {
  form.value = { uuid: '', legacyId: '', name: '' };
  document.getElementById('categoryIncomeModalLabel').innerText = 'Add New Income Category';
  const modal = new window.bootstrap.Modal(document.getElementById('categoryIncomeModal'));
  modal.show();
}

function isUuid(v){ return typeof v === 'string' && /^[0-9a-fA-F-]{8,}$/.test(v); }

async function updateKategoriIncome(idOrUuid) {
  form.value = { uuid: '', legacyId: '', name: '' };
  document.getElementById('categoryIncomeModalLabel').innerText = 'Edit Income Category';
  const modalEl = document.getElementById('categoryIncomeModal');
  const modal = new window.bootstrap.Modal(modalEl);
  modal.show();
  try {
    try { const el = document.querySelector('.preloader'); if (el) el.style.display = 'block'; } catch {}
    const params = isUuid(idOrUuid) ? { uuid: idOrUuid } : { id: idOrUuid };
    const res = await window.axios.get('/pages/admin/category/income/show', { params });
    const data = res.data || {};
    form.value.uuid = data.uuid || '';
    form.value.legacyId = data.id || '';
    if (data.name_category_incomes === '[encrypted]' && data.name_category_incomes_pgp) {
      try { form.value.name = await decryptArmor(data.name_category_incomes_pgp); } catch { form.value.name = ''; }
    } else {
      form.value.name = data.name_category_incomes || '';
    }
  } finally {
    try { const el = document.querySelector('.preloader'); if (el) el.style.display = 'none'; } catch {}
  }
}

function deleteKategoriIncome(idOrUuid) {
  window.Swal.fire({
    title: 'Are you sure?',
    text: 'Data will be deleted!',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Yes',
    cancelButtonText: 'Cancel'
  }).then((result) => {
    if (!result.isConfirmed) return;
    (async () => {
      try {
        try { const el = document.querySelector('.preloader'); if (el) el.style.display = 'block'; } catch {}
        const data = isUuid(idOrUuid) ? { uuid: idOrUuid } : { id: idOrUuid };
        await window.axios.delete('/pages/admin/category/income/delete', { data });
        window.$('#categoryIncomeTable').DataTable().ajax.reload();
        window.showCustomAlert?.('success', 'Data deleted successfully');
      } catch (e) {
        window.showCustomAlert?.('danger', e?.response?.data?.message || 'Delete failed');
      } finally {
        try { const el = document.querySelector('.preloader'); if (el) el.style.display = 'none'; } catch {}
      }
    })();
  });
}

async function onSubmit() {
  try {
    saving.value = true;
    const fd = new FormData();
    if (form.value.uuid) {
      fd.append('uuid', form.value.uuid);
    } else if (form.value.legacyId) {
      fd.append('id_category_income', form.value.legacyId);
    }
    fd.append('_token', document.querySelector('meta[name="csrf-token"]').content);

    // Try E2EE encrypt using user's public key
    let armored = null;
    try { armored = await encryptForSelf(form.value.name); } catch { armored = null; }
    if (armored) {
      fd.append('name_category_incomes', '[encrypted]');
      fd.append('name_category_incomes_pgp', armored);
    } else {
      fd.append('name_category_incomes', form.value.name);
    }

    await window.axios.post('/pages/admin/category/income/store', fd);
    const modal = window.bootstrap.Modal.getInstance(document.getElementById('categoryIncomeModal'));
    modal?.hide();
    window.$('#categoryIncomeTable').DataTable().ajax.reload();
    window.showCustomAlert?.('success', 'Saved');
  } catch (e) {
    window.showCustomAlert?.('danger', 'Failed to save');
  } finally {
    saving.value = false;
  }
}

onMounted(async () => {
  // Initialize E2EE store first to avoid race conditions
  try {
    await e2ee.initialize();
  } catch (error) {
    console.warn('[Admin CategoryIncome] Failed to initialize E2EE:', error);
  }
  
  const $ = window.$;
  if (!$) return;
  const currentUrl = window.location.pathname; // Controller returns datatables JSON when ajax()
  
  // Destroy existing DataTable if it exists
  if ($.fn.DataTable.isDataTable('#categoryIncomeTable')) {
    $('#categoryIncomeTable').DataTable().destroy();
  }
  
  dt = $('#categoryIncomeTable').DataTable({
    processing: true,
    serverSide: false,
    ajax: { url: currentUrl + '/list', dataSrc: '' },
    columns: [
      {
        data: null,
        name: 'no',
        className: 'text-center',
        render: function (data, type, row, meta) {
          const start = (typeof meta?.settings?._iDisplayStart === 'number')
            ? meta.settings._iDisplayStart
            : parseInt(meta?.settings?._iDisplayStart, 10) || 0;
          const n = Number(meta.row) + start + 1;
          return n;
        }
      },
      {
        data: 'name_category_incomes',
        name: 'name_category_incomes',
        render: function (data, type, row) {
          if (type === 'filter' || type === 'sort') {
            return row && row.name_category_incomes ? row.name_category_incomes : '';
          }
          try {
            if (row && row.name_category_incomes === '[encrypted]' && row.name_category_incomes_pgp) {
              const pgp = encodeURIComponent(row.name_category_incomes_pgp);
              return `<span class="enc-name" data-pgp="${pgp}"><span class="spinner-border spinner-border-sm me-1"></span>Decrypting...</span>`;
            }
          } catch {}
          return data ?? '';
        }
      },
      { data: 'created_at', name: 'created_at' },
      { data: 'updated_at', name: 'updated_at' },
      { data: 'action', searchable: false, sortable: false }
    ],
    columnDefs: [
      { targets: 0, className: 'text-center', width: '60px', orderable: false },
      { targets: 4, className: 'text-end', orderable: false }
    ],
    language: {
      search: '',
      searchPlaceholder: 'Search...',
      zeroRecords: 'No data available!',
      info: 'Showing _START_ to _END_ of _TOTAL_ entries',
      lengthMenu: 'Show _MENU_ entries',
      paginate: { previous: 'Previous', next: 'Next' }
    },
    drawCallback: async function(){
      try { window.feather?.replace?.(); } catch {}
      try { await decryptVisibleRows(); } catch {}
    },
    initComplete: function () {
      const $ = window.$;
      const lengthEl = $('.dataTables_length');
      const filterEl = $('.dataTables_filter');

      // Beautify controls
      lengthEl.find('label').addClass('mb-0 me-2');
      lengthEl.find('select').addClass('form-select form-select-sm d-inline-block').css('width','80px');
      filterEl.find('label').addClass('mb-0');
      // Remove static label text and style input
      filterEl.find('label').contents().filter(function(){ return this.nodeType === 3; }).remove();
      filterEl.find('input').addClass('form-control form-control-sm d-inline-block').attr('placeholder','Search...').css('width','260px');

      const header = $('<div class="row g-2 align-items-center mb-3 dt-header"></div>');
      const left = $('<div class="col-md-6"></div>').append(lengthEl);
      const right = $('<div class="col-md-6 text-md-end"></div>').append(filterEl);
      header.append(left).append(right);

      const wrapper = $('#categoryIncomeTable').closest('.dataTables_wrapper');
      header.insertBefore(wrapper.find('.row').first());
    }
  });

  // Decrypt all rows after data load so search works on plaintext
  $('#categoryIncomeTable').on('xhr.dt', async function(){
    try { await decryptAllRows(); } catch {}
  });

  // Expose handlers for action buttons generated by server HTML
  window.updateKategoriIncome = updateKategoriIncome;
  window.deleteKategoriIncome = deleteKategoriIncome;
});

// ---- Decryption helpers (PGP) ----
const e2ee = useE2EE();
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
  const api = $('#categoryIncomeTable').DataTable();
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
  const $ = window.$;
  const api = $('#categoryIncomeTable').DataTable();
  const rows = api.rows().indexes();
  for (let i = 0; i < rows.length; i++) {
    const idx = rows[i];
    const d = api.row(idx).data();
    if (d && d.name_category_incomes === '[encrypted]' && d.name_category_incomes_pgp) {
      try {
        const txt = await decryptArmor(d.name_category_incomes_pgp);
        d.name_category_incomes = txt;
        api.row(idx).data(d);
      } catch {}
    }
  }
  api.draw(false);
}

// Setelah kunci dibuka, dekripsi SEMUA baris agar pencarian client-side bekerja
watch(() => e2ee.isUnlocked?.value, (v) => {
  if (v) setTimeout(() => { try { decryptAllRows(); } catch {} }, 0);
});
</script>
