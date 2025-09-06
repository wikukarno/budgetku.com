<template>
  <MainLayout title="Income - Admin" page-title="Income" :requireUnlock="true">
    <div class="card bg-white border-0 rounded-3 mb-4">
      <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
          <h3 class="mb-0">Income</h3>
          <Link class="btn btn-primary" href="/pages/admin/income/create">
            <i data-feather="plus" class="me-2"></i>
            Add New
          </Link>
        </div>

        <div class="default-table-area all-products">
          <div class="table-responsive">
            <table class="table align-middle" id="incomeTable">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Category</th>
                  <th>Salary</th>
                  <th>Date</th>
                  <th>Description</th>
                  <th>Action</th>
                </tr>
              </thead>
            </table>
          </div>
        </div>
      </div>
    </div>
  </MainLayout>
</template>

<script setup>
import { onMounted, watch } from 'vue';
import MainLayout from '../../layouts/MainLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import { useE2EE } from '../../stores/e2ee.js';

const e2ee = useE2EE();

async function getDecryptedPrivateKey() {
  if (getDecryptedPrivateKey.cache) return getDecryptedPrivateKey.cache;
  
  // Ensure E2EE store is initialized before proceeding
  await e2ee.fetchUserKeys();
  const keys = e2ee.userKeys?.value || null;
  if (!keys || !keys.pgp_private_key_armor) throw new Error('No private key');
  
  let R = e2ee.Rb64?.value || null; 
  try { if (!R) R = sessionStorage.getItem('e2ee_R_b64') || null; } catch {}
  if (!R) throw new Error('E2EE is locked');
  
  const priv = await window.openpgp.readPrivateKey({ armoredKey: keys.pgp_private_key_armor });
  const dec = await window.openpgp.decryptKey({ privateKey: priv, passphrase: R });
  getDecryptedPrivateKey.cache = dec; return dec;
}
async function decryptArmor(armoredMessage) {
  const message = await window.openpgp.readMessage({ armoredMessage });
  const privateKey = await getDecryptedPrivateKey();
  const { data } = await window.openpgp.decrypt({ message, decryptionKeys: privateKey });
  return data;
}

function deleteIncome(uuid){
  window.Swal.fire({
    title: 'Are you sure?', text: 'Data will be deleted!', icon: 'warning',
    showCancelButton: true, confirmButtonColor: '#3085d6', cancelButtonColor: '#d33',
    confirmButtonText: 'Yes', cancelButtonText: 'Cancel'
  }).then((result) => {
    if (!result.isConfirmed) return;
    (async () => {
      try {
        await window.axios.delete('/pages/admin/income/delete', { data: { uuid } });
        window.$('#incomeTable').DataTable().ajax.reload();
        window.showCustomAlert?.('success', 'Deleted');
      } catch (e) {
        const msg = e?.response?.data?.message || 'Failed to delete';
        window.showCustomAlert?.('danger', msg);
      }
    })();
  });
}

onMounted(async () => {
  // Initialize E2EE store first to avoid race conditions
  try {
    await e2ee.initialize();
  } catch (error) {
    console.warn('[Admin Income] Failed to initialize E2EE:', error);
  }
  
  const $ = window.$; if (!$) return;
  
  // Destroy existing DataTable if it exists
  if ($.fn.DataTable.isDataTable('#incomeTable')) {
    $('#incomeTable').DataTable().destroy();
  }
  
  const dt = $('#incomeTable').DataTable({
    processing: true,
    serverSide: false,
    ajax: { url: window.location.pathname + '/list', dataSrc: '' },
    columns: [
      { data: null, render: (data, type, row, meta) => {
          const start = (typeof meta?.settings?._iDisplayStart === 'number')
            ? meta.settings._iDisplayStart
            : (parseInt(meta?.settings?._iDisplayStart, 10) || 0);
          return Number(meta.row) + start + 1;
        }
      },
      { data: 'category_name', name: 'category_name', render: (data,type,row) => {
          if (type==='filter' || type==='sort') return row.category_name || '';
          if (row.category_name === '[encrypted]' && row.category_name_pgp) {
            const pgp = encodeURIComponent(row.category_name_pgp);
            return `<span class=\"enc-cat\" data-pgp=\"${pgp}\"><span class=\"spinner-border spinner-border-sm me-1\"></span>Decrypting...</span>`;
          }
          return data ?? '';
        }
      },
      { data: 'salary_fmt', name: 'salary', render: (data,type,row) => {
          if (type==='filter' || type==='sort') return data ?? '';
          if (row.salary === '[encrypted]' && row.salary_pgp) {
            const pgp = encodeURIComponent(row.salary_pgp);
            return `<span class=\"enc-salary\" data-pgp=\"${pgp}\"><span class=\"spinner-border spinner-border-sm me-1\"></span>Decrypting...</span>`;
          }
          return data ?? '';
        }
      },
      { data: 'date_human', name: 'date' },
      { data: 'description', name: 'description', render: (data,type,row) => {
          if (type==='filter' || type==='sort') return row.description || '';
          if (row.description === '[encrypted]' && row.description_pgp) {
            const pgp = encodeURIComponent(row.description_pgp);
            return `<span class=\"enc-desc\" data-pgp=\"${pgp}\"><span class=\"spinner-border spinner-border-sm me-1\"></span>Decrypting...</span>`;
          }
          return data ?? '';
        }
      },
      { data: 'action', name: 'action', orderable: false, searchable: false },
    ],
    language: {
      search: '', searchPlaceholder: 'Search...', zeroRecords: 'No data available!',
      info: 'Showing _START_ to _END_ of _TOTAL_ entries', lengthMenu: 'Show _MENU_ entries',
      paginate: { previous: 'Previous', next: 'Next' }
    },
    drawCallback: function(){
      try { window.feather?.replace?.(); } catch {}
      try {
        const api = $('#incomeTable').DataTable();
        api.rows({ page: 'current' }).every(function(){
          const tr = this.node();
          const tdCat = $('td', tr).eq(1);
          const encCat = tdCat.find('.enc-cat');
          if (encCat.length && encCat.data('dec') !== 1) {
            const armored = encCat.attr('data-pgp');
            decryptArmor(decodeURIComponent(armored)).then(txt => { tdCat.text(txt); encCat.data('dec',1); }).catch(()=>{});
          }
          const tdSalary = $('td', tr).eq(2);
          const encSalary = tdSalary.find('.enc-salary');
          if (encSalary.length && encSalary.data('dec') !== 1) {
            const armored = encSalary.attr('data-pgp');
            decryptArmor(decodeURIComponent(armored)).then(val => {
              const n = Number(String(val).replace(/[^\d]/g,'')) || 0;
              const formatted = 'Rp. ' + n.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
              tdSalary.text(formatted); encSalary.data('dec',1);
            }).catch(()=>{});
          }
          const tdDesc = $('td', tr).eq(4);
          const encDesc = tdDesc.find('.enc-desc');
          if (encDesc.length && encDesc.data('dec') !== 1) {
            const armored = encDesc.attr('data-pgp');
            decryptArmor(decodeURIComponent(armored)).then(txt => { tdDesc.text(txt); encDesc.data('dec',1); }).catch(()=>{});
          }
        });
        // Intercept edit links for Inertia navigation
        const wrapper = $('#incomeTable').closest('.dataTables_wrapper');
        wrapper.find('a[href^="/pages/admin/income/edit/"]').off('click.inertia').on('click.inertia', function(ev){
          ev.preventDefault();
          const href = this.getAttribute('href');
          if (href) router.visit(href);
        });
      } catch {}
    },
  });
  window.deleteIncome = deleteIncome;

  // Decrypt all rows post-load so search works on plaintext
  async function decryptAllRows(){
    try {
      const api = $('#incomeTable').DataTable();
      const idxs = api.rows().indexes();
      const priv = await getDecryptedPrivateKey();
      for (let i = 0; i < idxs.length; i++) {
        const idx = idxs[i];
        const row = api.row(idx).data();
        if (!row) continue;
        if (row.category_name === '[encrypted]' && row.category_name_pgp) {
          try { const msg = await window.openpgp.readMessage({ armoredMessage: row.category_name_pgp }); const { data } = await window.openpgp.decrypt({ message: msg, decryptionKeys: priv }); row.category_name = data; } catch {}
        }
        if (row.salary === '[encrypted]' && row.salary_pgp) {
          try { const msg = await window.openpgp.readMessage({ armoredMessage: row.salary_pgp }); const { data } = await window.openpgp.decrypt({ message: msg, decryptionKeys: priv }); const n = Number(String(data).replace(/[^\d]/g,''))||0; row.salary_fmt = 'Rp. ' + n.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.'); } catch {}
        }
        if (row.description === '[encrypted]' && row.description_pgp) {
          try { const msg = await window.openpgp.readMessage({ armoredMessage: row.description_pgp }); const { data } = await window.openpgp.decrypt({ message: msg, decryptionKeys: priv }); row.description = data; } catch {}
        }
        api.row(idx).data(row);
      }
      api.draw(false);
    } catch {}
  }

  $('#incomeTable').on('xhr.dt', async function(){ try { await decryptAllRows(); } catch {} });
});

watch(() => e2ee.isUnlocked?.value, (v) => { if (v) { try { window.$('#incomeTable').DataTable().ajax.reload(null, false); } catch {} } });
</script>
