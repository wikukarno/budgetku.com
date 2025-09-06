<template>
  <MainLayout title="Expense - Admin" page-title="Expense" :requireUnlock="true">
    <div class="card bg-white border-0 rounded-3 mb-4">
      <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
          <h3 class="mb-0">Expense</h3>
          <Link class="btn btn-primary" href="/pages/admin/expense/create">
            <i data-feather="plus" class="me-2"></i>
            Add New
          </Link>
        </div>

        <div class="default-table-area all-products">
          <div class="table-responsive">
            <table class="table align-middle" id="expenseTable">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Category</th>
                  <th>Name</th>
                  <th>Price</th>
                  <th>Date</th>
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
import { Link, router } from '@inertiajs/vue3';
import MainLayout from '../../layouts/MainLayout.vue';
import { useE2EE } from '../../stores/e2ee.js';

const e2ee = useE2EE();

async function getPriv(){
  if (getPriv.cache) return getPriv.cache;
  
  // Ensure E2EE store is initialized before proceeding
  await e2ee.fetchUserKeys();
  const keys = e2ee.userKeys?.value || null;
  if (!keys?.pgp_private_key_armor) throw new Error('No private key');
  
  let R = e2ee.Rb64?.value || null; 
  try { if (!R) R = sessionStorage.getItem('e2ee_R_b64') || null; } catch{}
  if (!R) throw new Error('E2EE is locked');
  
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

function deleteExpense(uuid){
  window.Swal.fire({ title: 'Are you sure?', text: 'Data will be deleted!', icon: 'warning', showCancelButton: true, confirmButtonText: 'Yes', cancelButtonText: 'Cancel' })
    .then((res) => { if (!res.isConfirmed) return; (async () => { try { await window.axios.delete('/pages/admin/expense/delete', { data: { uuid } }); window.$('#expenseTable').DataTable().ajax.reload(); window.showCustomAlert?.('success','Deleted'); } catch (e) { window.showCustomAlert?.('danger','Failed to delete'); } })(); });
}

onMounted(async () => {
  // Initialize E2EE store first to avoid race conditions
  try {
    await e2ee.initialize();
  } catch (error) {
    console.warn('[Admin Expense] Failed to initialize E2EE:', error);
  }
  
  const $ = window.$; if (!$) return;
  
  // Destroy existing DataTable if it exists
  if ($.fn.DataTable.isDataTable('#expenseTable')) {
    $('#expenseTable').DataTable().destroy();
  }
  
  const dt = $('#expenseTable').DataTable({
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
      { data: 'name_item', name: 'name_item', render: (data,type,row) => {
          if (type==='filter' || type==='sort') return row.name_item || '';
          if (row.name_item === '[encrypted]' && row.name_item_pgp) {
            return `<span class=\"enc-name\" data-pgp=\"${encodeURIComponent(row.name_item_pgp)}\"><span class=\"spinner-border spinner-border-sm me-1\"></span>Decrypting...</span>`;
          }
          return data ?? '';
        }
      },
      { data: 'price', name: 'price', render: (data,type,row) => {
          if (type==='filter' || type==='sort') return data ?? '';
          if (row.price === '[encrypted]' && row.price_pgp) {
            const pgp = encodeURIComponent(row.price_pgp);
            return `<span class=\"enc-price\" data-pgp=\"${pgp}\"><span class=\"spinner-border spinner-border-sm me-1\"></span>Decrypting...</span>`;
          }
          return data ?? '';
        }
      },
      { data: 'purchase_date_human', name: 'purchase_date' },
      { data: 'action', searchable: false, orderable: false }
    ],
    drawCallback: function(){
      try { window.feather?.replace?.(); } catch {}
      try {
        const api = $('#expenseTable').DataTable();
        api.rows({ page: 'current' }).every(function(){
          const tr = this.node();
          const tdCat = $('td', tr).eq(1);
          const encCat = tdCat.find('.enc-cat');
          if (encCat.length && encCat.data('dec') !== 1) {
            const armored = encCat.attr('data-pgp');
            decryptArmor(decodeURIComponent(armored)).then(txt => { tdCat.text(txt); encCat.data('dec',1); }).catch(()=>{});
          }
          const tdName = $('td', tr).eq(2);
          const encName = tdName.find('.enc-name');
          if (encName.length && encName.data('dec') !== 1) {
            const armored = encName.attr('data-pgp');
            decryptArmor(decodeURIComponent(armored)).then(txt => { tdName.text(txt); encName.data('dec',1); }).catch(()=>{});
          }
          const tdPrice = $('td', tr).eq(3);
          const encPrice = tdPrice.find('.enc-price');
          if (encPrice.length && encPrice.data('dec') !== 1) {
            const armored = encPrice.attr('data-pgp');
            decryptArmor(decodeURIComponent(armored)).then(val => {
              const n = Number(String(val).replace(/[^\d]/g,'')) || 0;
              const formatted = 'Rp. ' + n.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
              tdPrice.text(formatted); encPrice.data('dec',1);
            }).catch(()=>{});
          }
        });
        // Intercept edit links for Inertia navigation
        const wrapper = $('#expenseTable').closest('.dataTables_wrapper');
        wrapper.find('a[href^="/pages/admin/expense/edit/"]').off('click.inertia').on('click.inertia', function(ev){
          ev.preventDefault();
          const href = this.getAttribute('href');
          if (href) router.visit(href);
        });
      } catch {}
    },
    language: { search:'', searchPlaceholder:'Search...', zeroRecords:'No data available!', info:'Showing _START_ to _END_ of _TOTAL_ entries', lengthMenu:'Show _MENU_ entries', paginate:{ previous:'Previous', next:'Next' } },
    columnDefs: [ { targets: 0, className:'text-center', width:'60px', orderable:false }, { targets: -1, className:'text-end', orderable:false } ]
  });

  window.deleteExpense = deleteExpense;

  // Decrypt all rows after data load so DataTables search works on plaintext
  async function decryptAllRows(){
    try {
      const api = $('#expenseTable').DataTable();
      const idxs = api.rows().indexes();
      const priv = await getPriv(); // ensure unlocked
      for (let i = 0; i < idxs.length; i++) {
        const idx = idxs[i];
        const row = api.row(idx).data();
        if (!row) continue;
        // Category name
        if (row.category_name === '[encrypted]' && row.category_name_pgp) {
          try {
            const msg = await window.openpgp.readMessage({ armoredMessage: row.category_name_pgp });
            const { data } = await window.openpgp.decrypt({ message: msg, decryptionKeys: priv });
            row.category_name = data;
          } catch {}
        }
        // Name item
        if (row.name_item === '[encrypted]' && row.name_item_pgp) {
          try {
            const msg = await window.openpgp.readMessage({ armoredMessage: row.name_item_pgp });
            const { data } = await window.openpgp.decrypt({ message: msg, decryptionKeys: priv });
            row.name_item = data;
          } catch {}
        }
        // Price
        if (row.price === '[encrypted]' && row.price_pgp) {
          try {
            const msg = await window.openpgp.readMessage({ armoredMessage: row.price_pgp });
            const { data } = await window.openpgp.decrypt({ message: msg, decryptionKeys: priv });
            const n = Number(String(data).replace(/[^\d]/g,'')) || 0;
            row.price = 'Rp. ' + n.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
          } catch {}
        }
        api.row(idx).data(row);
      }
      api.draw(false);
    } catch {}
  }

  $('#expenseTable').on('xhr.dt', async function(){ try { await decryptAllRows(); } catch {} });
});

watch(() => e2ee.isUnlocked?.value, (v) => { if (v) { try { window.$('#expenseTable').DataTable().ajax.reload(null, false); } catch {} } });
</script>
