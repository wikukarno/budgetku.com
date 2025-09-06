@extends('layouts.v2.app')

@section('title', 'Income Category')
    
@section('content')
    <div class="card bg-white border-0 rounded-3 mb-4">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
                <h3 class="mb-0">
                    Income Category
                </h3>

                <button class="btn btn-primary" onclick="addCategoryIncome();" type="button">
                    <i data-feather="plus" class="me-2"></i>
                    Add New
                </button>
            </div>
    
            <div class="default-table-area all-products">
                <div class="table-responsive">
                    <table class="table align-middle" id="categoryIncomeTable">
                        <thead>
                            <tr>
                                <th scope="col">Number</th>
                                <th scope="col">Income</th>
                                <th scope="col">Date</th>
                                <th scope="col">Description</th>
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

    <div class="modal fade" id="categoryIncomeModal" tabindex="-1" aria-labelledby="categoryIncomeModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="categoryIncomeModalLabel"></h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="form-tambah-kategori-income" method="POST">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="uuid" id="id_category_income">
                        <div class="form-group">
                            <label for="name">
                                Name
                            </label>
                            <input type="text" name="name_category_incomes" id="name_category_incomes" class="form-control"
                                placeholder="Salary or other" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger text-white" data-bs-dismiss="modal">
                            Cancel
                        </button>
                        <button type="submit" id="btnSaveKategoriKeuangan" class="btn btn-primary">
                            Save
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('after-scripts')
    

    <script>
        // Axios CSRF sudah diset global via meta tag + bootstrap.js

        // ---- Minimal E2EE helpers for encrypt/decrypt ----
        let cachedKeys = null;
        let unlockedPriv = null;

        async function getE2EEKeys() {
            if (cachedKeys) return cachedKeys;
            const res = await axios.get('{{ url('/e2ee/keys') }}');
            cachedKeys = res.data;
            return cachedKeys;
        }

        async function promptUnlockPrivateKey() {
            if (unlockedPriv) return unlockedPriv;
            // Try session cache first
            try {
                // small polling to allow SharedWorker to attach after load
                async function pollGetR(timeoutMs = 1000) {
                    const start = Date.now();
                    while (Date.now() - start < timeoutMs) {
                        let r = (window.E2EESession?.getR && window.E2EESession.getR()) || sessionStorage.getItem('e2ee_R_b64');
                        if (r) return r;
                        if (window.KeyWorker?.getR) {
                            try { r = await window.KeyWorker.getR(); } catch (e) { r = null; }
                            if (r) return r;
                        }
                        await new Promise(res => setTimeout(res, 100));
                    }
                    return null;
                }

                let cachedR = (window.E2EESession?.getR && window.E2EESession.getR()) || sessionStorage.getItem('e2ee_R_b64');
                if (!cachedR) cachedR = await pollGetR(1000);
                if (!cachedR && window.KeyWorker?.getR) {
                    try { const Rb64 = await window.KeyWorker.getR(); if (Rb64) { window.E2EESession?.setR?.(Rb64, { persist:false }); cachedR = Rb64; } } catch (e) {}
                }
                if (cachedR) {
                    const keys = await getE2EEKeys();
                    const priv = await openpgp.readPrivateKey({ armoredKey: keys.pgp_private_key_armor });
                    unlockedPriv = await openpgp.decryptKey({ privateKey: priv, passphrase: cachedR });
                    return unlockedPriv;
                }
            } catch (e) { /* ignore */ }
            const keys = await getE2EEKeys();
            if (!keys || !keys.e2ee_enabled) throw new Error('E2EE not enabled');
            const pass = await Swal.fire({
                title: 'Unlock Encryption',
                input: 'password',
                inputLabel: 'Enter your E2EE passphrase',
                inputAttributes: { autocapitalize: 'off' },
                showCancelButton: true,
                confirmButtonText: 'Unlock'
            }).then(r => r.isConfirmed ? r.value : null);
            if (!pass) throw new Error('Unlock cancelled');

            // Derive key from passphrase to unwrap R
            const wrapBytes = (b64) => Uint8Array.from(atob(b64), c => c.charCodeAt(0));
            const wrap = wrapBytes(keys.e2ee_pass_wrap);
            const salt = wrapBytes(keys.e2ee_pass_salt);
            const iv = wrap.slice(0,12);
            const ct = wrap.slice(12);

            let pdk;
            if (keys.e2ee_kdf_params?.kdf === 'argon2id' && window.argon2) {
                const params = keys.e2ee_kdf_params || {};
                const res = await window.argon2.hash({ pass, salt, type: window.argon2.ArgonType.Argon2id, mem: params.mem || 65536, time: params.time || 3, parallelism: params.parallelism || 1, hashLen: 32, raw: true });
                pdk = await crypto.subtle.importKey('raw', res.hash, { name: 'AES-GCM' }, false, ['decrypt']);
            } else {
                const baseKey = await crypto.subtle.importKey('raw', new TextEncoder().encode(pass), 'PBKDF2', false, ['deriveKey']);
                const iter = Math.max(310000, (keys.e2ee_kdf_params?.iter || 0));
                pdk = await crypto.subtle.deriveKey({ name: 'PBKDF2', salt, iterations: iter, hash: 'SHA-256' }, baseKey, { name: 'AES-GCM', length: 256 }, false, ['decrypt']);
            }
            const Rbuf = await crypto.subtle.decrypt({ name: 'AES-GCM', iv }, pdk, ct);
            const Rb64 = btoa(String.fromCharCode(...new Uint8Array(Rbuf)));
            try { window.E2EESession?.setR?.(Rb64); } catch (e) {}

            // Unlock private key with R
            const priv = await openpgp.readPrivateKey({ armoredKey: keys.pgp_private_key_armor });
            unlockedPriv = await openpgp.decryptKey({ privateKey: priv, passphrase: Rb64 });
            return unlockedPriv;
        }

        async function encryptTextForSelf(plain) {
            const keys = await getE2EEKeys();
            const pub = await openpgp.readKey({ armoredKey: keys.pgp_public_key });
            const message = await openpgp.createMessage({ text: plain });
            return await openpgp.encrypt({ message, encryptionKeys: pub });
        }

        async function decryptTextFromArmor(armor) {
            const priv = await promptUnlockPrivateKey();
            const message = await openpgp.readMessage({ armoredMessage: armor });
            const { data } = await openpgp.decrypt({ message, decryptionKeys: priv });
            return data;
        }

        function addCategoryIncome() {
            $('#categoryIncomeModal').modal('show');
            $('#categoryIncomeModalLabel').html('Add New Income Category');
            $('#id_category_income').val('');
            $('#form-tambah-kategori-income').trigger('reset');
            $('#btnSaveKategoriKeuangan').html('Save');
            $('#btnSaveKategoriKeuangan').attr('disabled', false);
        }

        function updateKategoriIncome(uuid){
            $('#form-tambah-kategori-income').trigger('reset');
            $('#categoryIncomeModal').modal('show');
            $('#categoryIncomeModalLabel').html('Edit Income Category');
            $('#id_category_income').val(uuid);
            $('#btnSaveKategoriKeuangan').html('Update');
            $('#btnSaveKategoriKeuangan').attr('disabled', false);
            
            $.ajax({
                type:"GET",
                url: "{{ route('customer.category.income.show') }}",
                data: {
                    "_token": "{{ csrf_token() }}",
                    uuid:uuid
                },
                dataType: 'json',
                beforeSend: function() {
                    $(".preloader").fadeIn();
                },
                success: function(res){
                    $('#id_kategori_income').val(res.uuid);
                    if (res.name_category_incomes_pgp) {
                        // Try decrypt; if user cancels, leave placeholder
                        (async () => {
                          try {
                            const keys = await getE2EEKeys();
                            const ver = res.content_key_version || keys.key_version || 1;
                            let privArmor = keys.pgp_private_key_armor;
                            if (ver !== (keys.key_version || 1)) {
                              const kp = await axios.get(`{{ url('/e2ee/keypair') }}/${ver}`);
                              privArmor = kp.data?.pgp_private_key_armor || privArmor;
                            }
                            // Ensure private key unlocked for this armor
                            const cachedR = (window.E2EESession?.getR && window.E2EESession.getR()) || sessionStorage.getItem('e2ee_R_b64');
                            if (!cachedR) throw new Error('Locked');
                            const priv = await openpgp.readPrivateKey({ armoredKey: privArmor });
                            const unlocked = await openpgp.decryptKey({ privateKey: priv, passphrase: cachedR });
                            const message = await openpgp.readMessage({ armoredMessage: res.name_category_incomes_pgp });
                            const { data } = await openpgp.decrypt({ message, decryptionKeys: unlocked });
                            $('#name_category_incomes').val(data);
                            // Lazy re-encrypt to latest key_version if needed
                            if (ver !== (keys.key_version || 1)) {
                              try {
                                const armor = await encryptTextForSelf(data);
                                const payload = new FormData();
                                payload.append('_token', '{{ csrf_token() }}');
                                payload.append('uuid', res.uuid);
                                payload.append('name_category_incomes', '[encrypted]');
                                payload.append('name_category_incomes_pgp', armor);
                                await axios.post("{{ route('customer.category.income.store') }}", payload);
                              } catch (e) { /* ignore */ }
                            }
                          } catch (e) {
                            $('#name_category_incomes').val('[encrypted]');
                          }
                        })()
                    } else {
                        $('#name_category_incomes').val(res.name_category_incomes);
                    }
                },
                complete: function(){
                    $(".preloader").fadeOut();
                }
            });
        }

        function deleteKategoriIncome(uuid){
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
                        url: "{{ route('customer.category.income.destroy') }}",
                        data: {
                            "_token": "{{ csrf_token() }}",
                            uuid:uuid
                        },
                        dataType: 'json',
                        beforeSend: function() {
                            $(".preloader").fadeIn();
                        },
                        success: function(res){
                            $('#categoryIncomeTable').DataTable().ajax.reload();
                            showCustomAlert('success', res.message);
                        },
                        complete: function(){
                            $(".preloader").fadeOut();
                        }
                    });
                }
            })
        }
        
        // replaced by E2EE-aware handler below

        (function initIncomeTable(){
            if (typeof window.$ !== 'function') { setTimeout(initIncomeTable, 50); return; }
            $('#categoryIncomeTable').dataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{!! url()->current() !!}",
            },
            columns: [
                { data: 'DT_RowIndex', name: 'uuid'},
                { data: 'name_category_incomes', name: 'name_category_incomes'},
                { data: 'created_at', name: 'created_at'},
                { data: 'updated_at', name: 'updated_at'},
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
            drawCallback: async function(settings) {
                try {
                    const api = $('#categoryIncomeTable').DataTable();
                    const rowsApi = api.rows({ page: 'current' });
                    const data = rowsApi.data();
                    let needDecrypt = false;
                    for (let i = 0; i < data.length; i++) {
                        if (data[i].name_category_incomes === '[encrypted]' && data[i].name_category_incomes_pgp) { needDecrypt = true; break; }
                    }
                    if (needDecrypt) await promptUnlockPrivateKey();
                    rowsApi.every(function() {
                        const d = this.data();
                        if (d.name_category_incomes === '[encrypted]' && d.name_category_incomes_pgp) {
                            const tr = this.node();
                            const nameTd = $('td', tr).eq(1);
                            decryptTextFromArmor(d.name_category_incomes_pgp)
                                .then(txt => nameTd.text(txt))
                                .catch(() => {});
                        }
                    });
                } catch (e) { }
            },
            initComplete: function () {
                const lengthEl = $('.dataTables_length');
                const filterEl = $('.dataTables_filter');
                
                const wrapper = $('<div class="dt-top w-100"></div>');
                lengthEl.appendTo(wrapper);
                filterEl.appendTo(wrapper);
                
                wrapper.insertBefore($('#categoryIncomeTable'));

                // Styling sekarang mengandalkan class bawaan DataTables (.dataTables_*/.dt-*) via CSS
                // Placeholder sudah di-set lewat opsi language.searchPlaceholder

            }
            });
        })();
    </script>

    <script>
        // Intercept form submit to encrypt name before sending
        (function initIncomeForm(){
            if (typeof window.$ !== 'function') { setTimeout(initIncomeForm, 50); return; }
            $('#form-tambah-kategori-income').on('submit', async function (e) {
            e.preventDefault();
            const btn = $('#btnSaveKategoriKeuangan');
            const originalText = btn.text();
            btn.attr('disabled', true).text('Saving...');

            try {
                const name = $('#name_category_incomes').val();
                const uuid = $('#id_category_income').val();
                let payload = new FormData();
                payload.append('_token', '{{ csrf_token() }}');
                if (uuid) payload.append('uuid', uuid);

                let encrypted = null;
                try {
                    encrypted = await encryptTextForSelf(name);
                } catch (err) {
                    // Fallback to plaintext if E2EE is not enabled
                }

                if (encrypted) {
                    // Store armor in *_pgp, keep a placeholder in plaintext field to satisfy validation
                    payload.append('name_category_incomes', '[encrypted]');
                    payload.append('name_category_incomes_pgp', encrypted);
                } else {
                    payload.append('name_category_incomes', name);
                }

                const res = await axios.post("{{ route('customer.category.income.store') }}", payload);
                if (res.data?.status === 'success') {
                    $('#categoryIncomeTable').DataTable().ajax.reload();
                    showCustomAlert('success', res.data.message || 'Saved');
                    $('#categoryIncomeModal').modal('hide');
                } else {
                    showCustomAlert('danger', res.data?.message || 'Failed to save');
                }
            } catch (err) {
                console.error(err);
                showCustomAlert('danger', 'Failed to save');
            } finally {
                btn.attr('disabled', false).text(originalText);
            }
            });
        })();
    </script>
@endpush
