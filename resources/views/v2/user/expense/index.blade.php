@extends('layouts.v2.app')

@section('title', 'Expense')

@section('content')
<div class="card bg-white border-0 rounded-3 mb-4">
    <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
            <h3 class="mb-0">
                Expense
            </h3>

            <a href="{{ route('customer.expense.create') }}" class="btn btn-primary" onclick="addCategoryIncome();" type="button">
                <i data-feather="plus" class="me-2"></i>
                Add New
            </a>
        </div>

        <div class="default-table-area all-products">
            <div class="table-responsive">
                <table class="table align-middle" id="expenseTable">
                    <thead>
                        <tr>
                            <th scope="col">Number</th>
                            <th scope="col">Category</th>
                            <th scope="col">
                                Name
                            </th>
                            <th scope="col">
                                Price
                            </th>
                            <th scope="col">
                                Date
                            </th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection

@push('after-scripts')
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

<script>
        function deleteExpense(uuid){
            Swal.fire({
                title: 'Are you sure?',
                text: "Data will be deleted!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type:"DELETE",
                        url: "{{ route('customer.expense.destroy') }}",
                        data: {
                            "_token": "{{ csrf_token() }}",
                            uuid:uuid
                        },
                        dataType: 'json',
                        beforeSend: function() {
                            $(".preloader").fadeIn();
                        },
                        success: function(res){
                            $('#expenseTable').DataTable().ajax.reload();
                            showCustomAlert('success', res.message);
                        },
                        complete: function(){
                            $(".preloader").fadeOut();
                        }
                    });
                }
            })
        }
        
        $('#expenseTable').dataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{!! url()->current() !!}",
            },
            columns: [
                { data: 'DT_RowIndex', name: 'uuid'},
                { data: 'category_finances_uuid', name: 'category_finances_uuid', render: function(data, type, row){
                    if (type === 'filter' || type === 'sort') return data || '';
                    if (data === '[encrypted]' && row.category_name_pgp) {
                        const pgp = encodeURIComponent(row.category_name_pgp);
                        return `<span class=\"enc-cat\" data-pgp=\"${pgp}\"><span class=\"spinner-border spinner-border-sm me-1\"></span>Decrypting...</span>`;
                    }
                    return data ?? '';
                }},
                { data: 'name_item', name: 'name_item'},
                { data: 'price', name: 'price', render: function(data, type, row){
                    if (type === 'filter' || type === 'sort') return data || '';
                    if (data === '[encrypted]' && row.price_pgp) {
                        const pgp = encodeURIComponent(row.price_pgp);
                        return `<span class=\"enc-price\" data-pgp=\"${pgp}\"><span class=\"spinner-border spinner-border-sm me-1\"></span>Decrypting...</span>`;
                    }
                    return data ?? '';
                }},
                { data: 'purchase_date', name: 'purchase_date'},
                {
                    data: 'action',
                    searchable: false,
                    sortable: false
                }
            ],
            language: {
                search: "",
                searchPlaceholder: "Search...",
                zeroRecords: "No data available!",
                info: "Showing _START_ to _END_ of _TOTAL_ entries",
                lengthMenu: "Show _MENU_ entries",
                paginate: {
                    previous: "Previous",
                    next: "Next"
                }
            },
            drawCallback: function(){
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
                        const tdPrice = $('td', tr).eq(3);
                        const encPrice = tdPrice.find('.enc-price');
                        if (encPrice.length && encPrice.data('dec') !== 1) {
                            const armored = encPrice.attr('data-pgp');
                            decryptArmor(decodeURIComponent(armored)).then(val => {
                                const n = Number(String(val).replace(/[^\d]/g,'')) || 0;
                                const formatted = 'Rp.' + n.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                                tdPrice.text(formatted); encPrice.data('dec',1);
                            }).catch(()=>{});
                        }
                    });
                } catch(e) {}
            },
            initComplete: function () {
                const lengthEl = $('.dataTables_length');
                const filterEl = $('.dataTables_filter');
                
                const wrapper = $('<div class="dt-top w-100"></div>');
                lengthEl.appendTo(wrapper);
                filterEl.appendTo(wrapper);
                
                wrapper.insertBefore($('#expenseTable'));

            }
        });

        // ---- E2EE decrypt helpers ----
        async function getPriv(){
            try {
                if (getPriv.cache) return getPriv.cache;
                const store = window.E2EESession;
                const keys = store?.userKeys?.value || null;
                if (!keys?.pgp_private_key_armor) throw new Error('No key');
                let R = store?.Rb64?.value || null; try { if (!R) R = sessionStorage.getItem('e2ee_R_b64') || null; } catch{}
                if (!R) throw new Error('Locked');
                const priv = await window.openpgp.readPrivateKey({ armoredKey: keys.pgp_private_key_armor });
                const dec = await window.openpgp.decryptKey({ privateKey: priv, passphrase: R });
                getPriv.cache = dec; return dec;
            } catch(e) { throw e; }
        }
        async function decryptArmor(armor){
            const msg = await window.openpgp.readMessage({ armoredMessage: armor });
            const priv = await getPriv();
            const { data } = await window.openpgp.decrypt({ message: msg, decryptionKeys: priv });
            return data;
        }
</script>
@endpush
