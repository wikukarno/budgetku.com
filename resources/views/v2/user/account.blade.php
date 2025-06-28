@extends('layouts.v2.app')

@section('title', 'My Account')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-12">
        <div class="card bg-primary border-0 rounded-3 welcome-box style-two mb-4 position-relative">
            <div class="card-body py-38 px-4">
                <div class="mb-5">
                    <h3 class="text-white fw-semibold">Welcome Back, <span class="text-danger-div">
                            {{ Auth::user()->name }}!</span></h3>
                    <p class="text-light">
                        Have a nice and blessed day.
                    </p>
                </div>

                {{-- <div class="d-flex align-items-center flex-wrap gap-4 gap-xxl-5">
                    <div class="d-flex align-items-center welcome-status-item style-two">
                        <div class="flex-shrink-0">
                            <i class="material-symbols-outlined">airplay</i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="text-white fw-semibold mb-0 fs-16">75h</h5>
                            <p class="text-light">Hours Spent</p>
                        </div>
                    </div>

                    <div class="d-flex align-items-center welcome-status-item style-two">
                        <div class="flex-shrink-0">
                            <i class="material-symbols-outlined icon-bg two">local_library</i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="text-white fw-semibold mb-0 fs-16">15</h5>
                            <p class="text-light">Course Completed</p>
                        </div>
                    </div>
                </div> --}}
            </div>
            <img src="{{ asset('v2/images/welcome-2.gif') }}" class="welcome-2 d-none d-sm-block" alt="welcome">
        </div>
    </div>
    {{-- <div class="col-lg-6">
        <div class="row justify-content-center">
            <div class="col-lg-4 col-sm-4">
                <div class="card bg-white border-0 rounded-3 mb-4">
                    <div class="card-body p-4">
                        <span>Total Projects</span>
                        <h3 class="mb-0 fs-20">22.5k+</h3>
                        <div class="py-3">
                            <div class="wh-77 lh-97 text-center m-auto bg-primary bg-opacity-25 rounded-circle">
                                <i class="material-symbols-outlined fs-32 text-primary">auto_stories</i>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fs-12">This Month</span>
                            <i class="material-symbols-outlined text-success">timeline</i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-sm-4">
                <div class="card bg-white border-0 rounded-3 mb-4">
                    <div class="card-body p-4">
                        <span>Total Orders</span>
                        <h3 class="mb-0 fs-20">25k+</h3>
                        <div class="py-3">
                            <div class="wh-77 lh-97 text-center m-auto bg-primary-div bg-opacity-25 rounded-circle">
                                <i class="material-symbols-outlined fs-32 text-primary-div">orders</i>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fs-12">This Month</span>
                            <i class="material-symbols-outlined text-danger">trending_down</i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-sm-4">
                <div class="card bg-white border-0 rounded-3 mb-4">
                    <div class="card-body p-4">
                        <span>Total Revenue</span>
                        <h3 class="mb-0 fs-20">$34.5m+</h3>
                        <div class="py-3">
                            <div class="wh-77 lh-97 text-center m-auto bg-danger bg-opacity-25 rounded-circle">
                                <i class="material-symbols-outlined fs-32 text-danger">payments</i>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fs-12">This Month</span>
                            <i class="material-symbols-outlined text-success">trending_up</i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> --}}
</div>
<div class="row">
    <div class="col-xxl-12">
        <div class="row">
            <div class="col-xxl-12 col-md-6 col-lg-4">
                <div class="card bg-white border-0 rounded-3 mb-4">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
                            <h3 class="mb-0">Account Settings</h3>
                        </div>

                        {{-- Tabs --}}
                        <ul class="nav nav-tabs mb-4" id="settingsTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="account-tab" data-bs-toggle="tab"
                                    data-bs-target="#account" type="button" role="tab">Account Settings</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="password-tab" data-bs-toggle="tab"
                                    data-bs-target="#password" type="button" role="tab">Change Password</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="twofa-tab" data-bs-toggle="tab" data-bs-target="#twofa"
                                    type="button" role="tab">
                                    Two-Factor Authentication
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="delete-tab" data-bs-toggle="tab" data-bs-target="#delete"
                                    type="button" role="tab">Delete Account</button>
                            </li>
                        </ul>

                        <div class="tab-content" id="settingsTabContent">
                            {{-- Account Settings --}}
                            <div class="tab-pane fade show active" id="account" role="tabpanel">
                                <div class="mb-4">
                                    <h4 class="fs-20 mb-1">Profile</h4>
                                    <p class="fs-15">Update your photo and personal details here.</p>
                                </div>
                                <form id="account-form">
                                    @csrf
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group mb-4">
                                                <label class="label text-secondary">Name</label>
                                                <div class="form-group position-relative">
                                                    <input type="text" name="name" value="{{ Auth::user()->name }}"
                                                        class="form-control text-dark ps-5 h-55" required>
                                                    <i
                                                        class="ri-user-line position-absolute top-50 start-0 translate-middle-y fs-20 text-gray-light ps-20"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group mb-4">
                                                <label class="label text-secondary">Email Address</label>
                                                <div class="form-group position-relative">
                                                    <input type="email" name="email" value="{{ Auth::user()->email }}"
                                                        class="form-control text-dark ps-5 h-55" readonly>
                                                    <i
                                                        class="ri-mail-line position-absolute top-50 start-0 translate-middle-y fs-20 text-gray-light ps-20"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group mb-4">
                                                <label class="label text-secondary">Parent Email (Optional)</label>
                                                <div class="form-group position-relative">
                                                    <input type="email" name="email_parrent"
                                                        value="{{ Auth::user()->email_parrent }}"
                                                        class="form-control text-dark ps-5 h-55">
                                                    <i
                                                        class="ri-mail-unread-line position-absolute top-50 start-0 translate-middle-y fs-20 text-gray-light ps-20"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="text-end">
                                        <button type="submit" class="btn btn-primary px-4">Update Profile</button>
                                    </div>
                                </form>
                            </div>

                            {{-- Change Password --}}
                            <div class="tab-pane fade" id="password" role="tabpanel">
                                <div class="mb-4">
                                    <h4 class="fs-20 mb-1">Change Password</h4>
                                    <p class="fs-15">Update your account password securely.</p>
                                </div>
                                <form id="change-password-form">
                                    @csrf
                                    <div class="mb-3">
                                        <label class="label">Current Password</label>
                                        <input type="password" name="current_password" class="form-control" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="label">New Password</label>
                                        <input type="password" name="new_password" class="form-control" required>
                                    </div>
                                    <div class="mb-4">
                                        <label class="label">Confirm New Password</label>
                                        <input type="password" name="new_password_confirmation" class="form-control"
                                            required>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Update Password</button>
                                </form>
                            </div>

                            {{-- Two-Factor Authentication --}}
                            <div class="tab-pane fade" id="twofa" role="tabpanel">
                                <div class="mb-4">
                                    <h4 class="fs-20 mb-1">Two-Factor Authentication</h4>
                                    <p class="fs-15 text-muted">Add an extra layer of security to your account using
                                        Google Authenticator.</p>
                                </div>

                                {{-- Enable 2FA --}}
                                <button class="btn btn-primary" id="btn-enable-2fa"
                                    @if(Auth::user()->two_factor_enabled) hidden @endif
                                    onclick="showModalTwoFA()">
                                    <i class="bi bi-shield-lock"></i> Enable Two-Factor Authentication
                                </button>

                                {{-- Disable 2FA --}}
                                <div id="disable-2fa-section" @if(!Auth::user()->two_factor_enabled) hidden @endif>
                                    <form id="disable-2fa-form">
                                        @csrf
                                        <button type="submit" class="btn btn-danger">
                                            <i class="bi bi-x-circle"></i> Disable Two-Factor Authentication
                                        </button>
                                    </form>
                                </div>
                            </div>

                            {{-- Delete Account --}}
                            <div class="tab-pane fade" id="delete" role="tabpanel">
                                <div class="mb-4">
                                    <h4 class="fs-20 text-danger mb-1">Delete Account</h4>
                                    <p class="fs-15">Once deleted, your account and all data will be permanently
                                        removed. This action cannot be undone.</p>
                                </div>
                                <form id="delete-account-form">
                                    @csrf
                                    <p class="text-muted mb-3">
                                        Once deleted, your account and all associated data will be <strong>permanently
                                            removed</strong>. This action
                                        cannot be undone.
                                    </p>
                                    <button type="submit" class="btn btn-danger text-white">
                                        Delete Account
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal for Two-Factor Authentication -->
<div class="modal fade" id="enableTwoFactor" tabindex="-1" aria-labelledby="enableTwoFactorLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="enableTwoFactorLabel"></h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="enable-2fa-form" method="POST">
                @csrf
                <div class="modal-body">
                    <!-- Optional setup section (hidden later) -->
                    <div id="enable-2fa-section">
                        <!-- your setup form here -->
                    </div>

                    <!-- QR Code section (initially hidden) -->
                    <div id="qr-section" hidden>
                        <div class="text-center mb-3">
                            <img id="qr-code-img" src="" alt="QR Code" class="img-fluid" />
                        </div>
                        <p class="text-center">Secret Key: <strong id="secret-key"></strong></p>
                    </div>

                    <!-- OTP Input Section -->
                    <div id="otp-section" hidden>
                        <div class="mb-3">
                            <label for="otp-code" class="form-label">Enter the 6-digit code from your Authenticator
                                App</label>
                            <input type="text" class="form-control text-center" id="otp-code" maxlength="6"
                                placeholder="123456" required>
                        </div>
                    </div>

                    <!-- Recovery Code Section -->
                    <div id="recovery-section" hidden>
                        <hr>
                        <h6 class="text-center mb-2">Recovery Codes</h6>
                        <p class="text-muted text-center">Please save these recovery codes securely.</p>
                        <ul id="recovery-codes" class="list-group mb-3 text-center">
                            <!-- akan diisi JS -->
                        </ul>
                        <div class="text-center">
                            <button type="button" class="btn btn-outline-primary btn-sm mb-2"
                                onclick="downloadRecoveryCodes()">
                                Download Codes
                            </button><br>
                            <small class="text-muted">You can close this modal after saving.</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger text-white" onclick="cancelTwoFA()">
                        Cancel
                    </button>
                    <button type="button" id="btnNext2FA" onclick="btnNextOtp()" class="btn btn-primary">
                        Next
                    </button>
                    <button type="button" id="btnVerify2FA" onclick="btnVerifyOtp()" class="btn btn-success text-white"
                        hidden>
                        Verify and Enable 2FA
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('after-scripts')
<script>
    // Buka tab berdasarkan URL hash saat halaman dimuat
        document.addEventListener('DOMContentLoaded', function () {
            const hash = window.location.hash;
            if (hash) {
                const tabTrigger = document.querySelector(`button[data-bs-target="${hash}"]`);
                if (tabTrigger) {
                    new bootstrap.Tab(tabTrigger).show();
                }
            }

            // Update hash di URL saat tab berubah
            const tabButtons = document.querySelectorAll('#settingsTab button[data-bs-toggle="tab"]');
            tabButtons.forEach(button => {
                button.addEventListener('shown.bs.tab', function (e) {
                    const target = e.target.getAttribute('data-bs-target');
                    history.replaceState(null, null, target); // ganti hash di URL
                });
            });
        });

        document.getElementById('account-form').addEventListener('submit', function(e) {
            e.preventDefault();
    
            let formData = new FormData(this);
    
            axios.post("{{ route('customer.account.update', Auth::id()) }}", formData, {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name=_token]').value,
                    'Content-Type': 'multipart/form-data',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                params: {
                    _method: 'PUT'
                }
            }).then(response => {
                console.log(response);
                if (response.data.status) {
                    showCustomAlert('success', response.data.message);
                } else {
                    showCustomAlert('danger', 'Failed to update profile');
                }
            }).catch(error => {
                console.error(error);
                showCustomAlert('danger', 'Something went wrong!');
            });
        });

        document.getElementById('change-password-form').addEventListener('submit', function (e) {
            e.preventDefault();

            let formData = new FormData(this);

            axios.post("{{ route('customer.account.password.update') }}", formData, {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name=_token]').value,
                    'Content-Type': 'multipart/form-data',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                params: {
                    _method: 'PUT'
                }
            }).then(response => {
                if (response.data.status) {
                    showCustomAlert('success', response.data.message);
                    document.getElementById('change-password-form').reset();
                } else {
                    showCustomAlert('danger', response.data.message);
                }
            }).catch(error => {
                showCustomAlert('danger', error.response.data.message || 'Something went wrong!');
            });
        });

        function showModalTwoFA() {

            document.getElementById('qr-code-img').src = '';
            document.getElementById('secret-key').textContent = '';
            document.getElementById('otp-code').value = '';
            document.getElementById('recovery-codes').innerHTML = '';
            document.getElementById('recovery-section').setAttribute('hidden', 'true');
            document.getElementById('otp-section').setAttribute('hidden', 'true');
            document.getElementById('qr-section').setAttribute('hidden', 'true');

            $('#enableTwoFactorLabel').text('Enable Two-Factor Authentication');
            $('#enableTwoFactor').modal('show');

            const form = document.getElementById('enable-2fa-form');
            const formData = new FormData(form);

            axios.post("{{ route('2fa.setup') }}", formData, {
                headers: {
                    'X-CSRF-TOKEN': formData.get('_token'),
                    'X-Requested-With': 'XMLHttpRequest'
                }
            }).then(res => {
                showCustomAlert('success', 'QR Code generated. Please scan and verify!');

                // Show QR code and reset others
                document.getElementById('qr-code-img').src = res.data.qr_code;
                document.getElementById('secret-key').textContent = res.data.secret;

                document.getElementById('enable-2fa-section')?.setAttribute('hidden', 'true');
                document.getElementById('qr-section')?.removeAttribute('hidden');
                document.getElementById('otp-section')?.setAttribute('hidden', 'true');

                document.getElementById('btnVerify2FA')?.setAttribute('hidden', 'true');
                document.getElementById('btnNext2FA')?.removeAttribute('hidden');
            }).catch(err => {
                console.error(err);
                showCustomAlert('danger', 'Failed to enable 2FA');
            });
        }

        function btnNextOtp() {
            document.getElementById('qr-section')?.setAttribute('hidden', 'true');
            document.getElementById('otp-section')?.removeAttribute('hidden');

            document.getElementById('btnVerify2FA')?.removeAttribute('hidden');
            document.getElementById('btnNext2FA')?.setAttribute('hidden', 'true');
        }

        function btnVerifyOtp() {
            const code = document.getElementById('otp-code').value;
            const form = document.getElementById('enable-2fa-form');
            const formData = new FormData(form);
            formData.append('code', code);

            if (code.trim().length !== 6) {
                showCustomAlert('danger', 'OTP code must be 6 digits');
                return;
            }

            axios.post("{{ route('2fa.verify') }}", formData, {
                headers: {
                    'X-CSRF-TOKEN': formData.get('_token'),
                    'X-Requested-With': 'XMLHttpRequest'
                }
            }).then(res => {
                showCustomAlert('success', 'Two-Factor Authentication enabled!');

                // Sembunyikan tombol next & verify
                document.getElementById('btnNext2FA')?.setAttribute('hidden', 'true');
                document.getElementById('btnVerify2FA')?.setAttribute('hidden', 'true');

                // Tampilkan recovery section
                document.getElementById('recovery-section')?.removeAttribute('hidden');

                // Populate recovery codes
                const list = document.getElementById('recovery-codes');
                list.innerHTML = '';
                res.data.recovery_codes?.forEach(code => {
                    const li = document.createElement('li');
                    li.className = 'list-group-item';
                    li.textContent = code;
                    list.appendChild(li);
                });

                // Sembunyikan tombol enable
                const enableBtn = document.getElementById('btn-enable-2fa');
                if (enableBtn) enableBtn.setAttribute('hidden', 'true');

                // Tampilkan tombol disable
                const disableSection = document.getElementById('disable-2fa-section');
                if (disableSection) {
                    disableSection.removeAttribute('hidden');
                } else {
                    // Render manual kalau belum ada
                    const container = document.querySelector('#twofa');
                    const html = `
                        <div id="disable-2fa-section">
                            <form id="disable-2fa-form">
                                @csrf
                                <button type="submit" class="btn btn-danger">
                                    <i class="bi bi-x-circle"></i> Disable Two-Factor Authentication
                                </button>
                            </form>
                        </div>
                    `;
                    container.insertAdjacentHTML('beforeend', html);
                    bindDisableForm(); // function ini harus ada di bawah
                }

            }).catch(err => {
                console.error(err);
                showCustomAlert('danger', err.response?.data?.message || 'Invalid OTP code!');
            });
        }

        function cancelTwoFA() {
            document.getElementById('enable-2fa-section')?.removeAttribute('hidden');
            document.getElementById('qr-section')?.setAttribute('hidden', 'true');
            document.getElementById('otp-section')?.setAttribute('hidden', 'true');

            document.getElementById('qr-code-img').src = '';
            document.getElementById('secret-key').textContent = '';
            document.getElementById('otp-code').value = '';

            document.getElementById('btnVerify2FA')?.setAttribute('hidden', 'true');
            document.getElementById('btnNext2FA')?.removeAttribute('hidden');

            $('#enableTwoFactor').modal('hide');
        }

        function downloadRecoveryCodes() {
            const listItems = document.querySelectorAll('#recovery-codes li');
            if (!listItems.length) {
                showCustomAlert('danger', 'No recovery codes to download');
                return;
            }

            const codes = Array.from(listItems).map(li => li.textContent);
            const blob = new Blob([codes.join('\n')], { type: 'text/plain' });
            const link = document.createElement('a');
            link.href = URL.createObjectURL(blob);
            link.download = 'recovery-codes.txt';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);

            showCustomAlert('success', 'Recovery codes downloaded.');

            // Optional: tandai ke backend
            axios.post("{{ route('2fa.mark.downloaded') }}", null, {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name=_token]').value,
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
        }


        // Disable 2FA
        document.getElementById('disable-2fa-form')?.addEventListener('submit', function (e) {
            e.preventDefault();
            Swal.fire({
                title: 'Are you sure?',
                text: 'This will disable 2FA on your account.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, disable it'
            }).then((result) => {
                if (result.isConfirmed) {
                    const formData = new FormData(this);

                    axios.post("{{ route('2fa.disable') }}", formData, {
                        headers: {
                            'X-CSRF-TOKEN': formData.get('_token'),
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    }).then(res => {
                        showCustomAlert('success', 'Two-Factor Authentication disabled.');
                        document.getElementById('disable-2fa-section')?.setAttribute('hidden', 'true');
                        document.getElementById('btn-enable-2fa')?.removeAttribute('hidden');
                    }).catch(() => {
                        showCustomAlert('danger', 'Failed to disable 2FA');
                    });
                }
            });
        });


        document.getElementById('delete-account-form').addEventListener('submit', function (e) {
            e.preventDefault();

            Swal.fire({
                title: 'Are you sure?',
                text: 'Your account will be permanently deleted!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    axios.post("{{ route('customer.account.delete') }}", null, {
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('input[name=_token]').value,
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        params: {
                            _method: 'DELETE'
                        }
                    }).then(response => {
                        if (response.data.status) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted!',
                                text: response.data.message,
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.href = "{{ route('login') }}";
                            });
                        } else {
                            Swal.fire('Error', response.data.message, 'error');
                        }
                    }).catch(() => {
                        Swal.fire('Error', 'Something went wrong!', 'error');
                    });
                }
            });
        });

</script>
@endpush