<template>
  <MainLayout title="Income" page-title="Income" :requireUnlock="true">
    <div class="card bg-white border-0 rounded-3 mb-4">
      <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
          <h3 class="mb-0">Income</h3>
          <Link class="btn btn-primary" href="/pages/customer/income/create">
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

    <!-- No modal; create/edit use separate pages -->
  </MainLayout>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount, watch } from 'vue';
import MainLayout from '../../layouts/MainLayout.vue';
import { useE2EE } from '../../stores/e2ee.js';
import { Link, router } from '@inertiajs/vue3';

const e2ee = useE2EE();
const categories = ref([]);

function showPreloader(){ try { const el = document.querySelector('.preloader'); if (el) el.style.display = 'block'; } catch {} }
function hidePreloader(){ try { const el = document.querySelector('.preloader'); if (el) el.style.display = 'none'; } catch {} }

async function encryptForSelf(plain) {
  const keys = e2ee.userKeys?.value || null;
  if (!keys || !keys.pgp_public_key) throw new Error('No public key');
  const publicKey = await window.openpgp.readKey({ armoredKey: keys.pgp_public_key });
  const message = await window.openpgp.createMessage({ text: plain });
  const armor = await window.openpgp.encrypt({ message, encryptionKeys: publicKey });
  return armor;
}
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

async function fetchCategories(){
  try {
    const res = await window.axios.get('/pages/customer/category/income/list');
    const raw = res.data || [];
    const out = [];
    for (const r of raw) {
      let name = r.name_category_incomes || '';
      if (name === '[encrypted]' && r.name_category_incomes_pgp) {
        try { name = await decryptArmor(r.name_category_incomes_pgp); } catch {}
      }
      out.push({ uuid: r.uuid, name });
    }
    categories.value = out;
  } catch {}
}

// No inline edit; now using edit page via link

function deleteIncome(uuid){
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
        showPreloader();
        await window.axios.delete('/pages/customer/income/delete', { data: { uuid } });
        window.$('#incomeTable').DataTable().ajax.reload();
        window.showCustomAlert?.('success', 'Deleted');
      } catch (e) {
        const msg = e?.response?.data?.message || 'Failed to delete';
        window.showCustomAlert?.('danger', msg);
      } finally { hidePreloader(); }
    })();
  });
}

onMounted(async () => {
  // Initialize E2EE store first to avoid race conditions
  try {
    await e2ee.initialize();
  } catch (error) {
    console.warn('[Income] Failed to initialize E2EE:', error);
  }
  
  await fetchCategories();
  const $ = window.$;
  
  // Destroy existing DataTable if it exists
  if ($.fn.DataTable.isDataTable('#incomeTable')) {
    $('#incomeTable').DataTable().destroy();
  }
  
  const dt = $('#incomeTable').DataTable({
    processing: true,
    serverSide: false,
    ajax: { url: window.location.pathname + '/list', dataSrc: '' },
    columns: [
      { data: null, render: (d,t,r,m) => (m.row + 1 + (m.settings?._iDisplayStart || 0)) },
      { data: 'category_name', name: 'category_name', render: (data,type,row) => {
          if (type==='filter' || type==='sort') return row.category_name || '';
          if (row.category_name === '[encrypted]' && row.category_name_pgp) {
            const pgp = encodeURIComponent(row.category_name_pgp);
            return `<span class=\"enc-cat\" data-pgp=\"${pgp}\"><span class=\"spinner-border spinner-border-sm me-1\"></span>Decrypting...</span>`;
          }
          return data ?? '';
        }
      },
      { data: 'salary_fmt', name: 'salary', render: (data, type, row) => {
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
      { data: null, searchable: false, orderable: false, render: (data, type, row) => {
          const uuid = row.uuid;
          const editHref = `/pages/customer/income/edit/${uuid}`;
          return `
            <div class="d-flex justify-content-end flex-column flex-sm-row flex-wrap gap-2">
              <a href="${editHref}" class="btn btn-warning btn-sm text-white" data-action="edit-income" data-uuid="${uuid}">
                <i class="ri-edit-line"></i><span class="ms-1">Edit</span>
              </a>
              <button type="button" class="btn btn-danger btn-sm text-white" data-action="delete-income" data-uuid="${uuid}">
                <i class="ri-delete-bin-line"></i><span class="ms-1">Delete</span>
              </button>
            </div>`;
        }
      }
    ],
    drawCallback: async function(){
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
          const tdDesc = $('td', tr).eq(4);
          const encDesc = tdDesc.find('.enc-desc');
          if (encDesc.length && encDesc.data('dec') !== 1) {
            const armored = encDesc.attr('data-pgp');
            decryptArmor(decodeURIComponent(armored)).then(txt => { tdDesc.text(txt); encDesc.data('dec',1); }).catch(()=>{});
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
        });
        // Intercept edit links for Inertia navigation
        const wrapper = $('#incomeTable').closest('.dataTables_wrapper');
        wrapper.find('a[href^="/pages/customer/income/edit/"]').off('click.inertia').on('click.inertia', function(ev){
          ev.preventDefault();
          const href = this.getAttribute('href');
          if (href) router.visit(href);
        });
      } catch {}
    },
    language: {
      search: '', searchPlaceholder: 'Search...', zeroRecords: 'No data available!',
      info: 'Showing _START_ to _END_ of _TOTAL_ entries', lengthMenu: 'Show _MENU_ entries', paginate: { previous: 'Previous', next: 'Next' }
    },
    columnDefs: [ { targets: 0, className: 'text-center', width: '60px', orderable: false }, { targets: -1, className: 'text-end', orderable: false } ]
  });

  // Delegated handlers for action buttons (edit/delete)
  const onActionClick = (e) => {
    const el = e.target?.closest?.('[data-action]');
    if (!el) return;
    const act = el.getAttribute('data-action');
    const uuid = el.getAttribute('data-uuid');
    if (!uuid) return;
    if (act === 'edit-income') { e.preventDefault(); router.visit(`/pages/customer/income/edit/${uuid}`); }
    if (act === 'delete-income') { e.preventDefault(); deleteIncome(uuid); }
  };
  document.addEventListener('click', onActionClick);

  onBeforeUnmount(() => {
    document.removeEventListener('click', onActionClick);
  });
});

watch(() => e2ee.isUnlocked?.value, (v) => { if (v) { try { window.$('#incomeTable').DataTable().ajax.reload(null, false); } catch {} } });
</script>
