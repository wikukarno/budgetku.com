@extends('layouts.auth')

@section('title', 'BudgetKu - Verify Two-Factor Authentication')

@section('content')
<div class="row align-items-center justify-content-center vh-100">
    <div class="col-lg-4 col-md-8 col-sm-10 mx-auto">
        <div class="ms-lg-auto text-center">
            <h3 class="fs-28 mb-2">Two-Factor Authentication</h3>
            <p class="fw-medium fs-16 mb-4">Please enter the 6-digit code from your authenticator app</p>

            @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                    <li class="mb-1">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form method="POST" action="{{ route('2fa.verify.login') }}" id="verify-form">
                @csrf
                <input type="hidden" name="source" value="otp">
                <div class="form-group mb-4">
                    <label class="label text-secondary">Authentication Code</label>
                    <div class="d-flex gap-2 justify-content-center">
                        @for ($i = 0; $i
                        < 6; $i++) <input type="text" inputmode="numeric" maxlength="1"
                            class="form-control text-center otp-input"
                            style="width: 50px; height: 55px; font-size: 24px;" id="otp-{{ $i }}" />
                        @endfor
                    </div>
                    <input type="hidden" name="code" id="otp-full-code" />
                </div>

                <div class="form-group mb-4">
                    <button type="submit" id="verify-btn" class="btn btn-primary fw-medium py-2 px-3 w-100">
                        <div class="d-flex align-items-center justify-content-center py-1">
                            <span class="spinner-border spinner-border-sm me-2 d-none" id="loading-spinner"
                                role="status" aria-hidden="true"></span>
                            <i class="material-symbols-outlined text-white fs-20 me-2">lock</i>
                            <span id="btn-text">Verify Code</span>
                        </div>
                    </button>
                </div>
            </form>
            <div class="text-center mb-3">
                <p class="text-secondary fs-14 mb-0">Don't have an authenticator app?</p>
            </div>
            <div class="text-center mt-4">
                <button class="btn btn-secondary fw-medium py-2 px-3 w-100" onclick="useRecoveryCode()">
                    <div class="d-flex align-items-center justify-content-center py-1">
                        <i class="material-symbols-outlined text-white fs-20 me-2">vpn_key</i>
                        Use Recovery Code
                    </div>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Recovery Code -->
<div class="modal fade" id="recoveryModal" tabindex="-1" aria-labelledby="recoveryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" action="{{ route('2fa.verify.login') }}" id="recovery-form">
            @csrf
            <input type="hidden" name="source" value="recovery">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="recoveryModalLabel">Enter Recovery Code</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted">Enter one of your 2FA recovery codes. This will only work once per code.</p>
                    <input type="text" name="code" class="form-control text-center" maxlength="255" required
                        placeholder="e.g. ABCD123456" />
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success text-white w-100" id="recovery-submit-btn">
                        <div class="d-flex align-items-center justify-content-center py-1">
                            <span class="spinner-border spinner-border-sm me-2 d-none" id="recovery-spinner" role="status"
                                aria-hidden="true"></span>
                            <i class="material-symbols-outlined text-white fs-20 me-2">vpn_key</i>
                            <span id="recovery-btn-text">Verify Recovery Code</span>
                        </div>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('after-scripts')
<script>

    const inputs = document.querySelectorAll('.otp-input');
    const hiddenInput = document.getElementById('otp-full-code');

    inputs.forEach((input, index) => {
        // Auto move to next
        input.addEventListener('input', function (e) {
            let val = e.target.value;

            // Only allow numeric
            if (!/^\d$/.test(val)) {
                e.target.value = '';
                return;
            }

            // Move to next
            if (val && index < inputs.length - 1) {
                inputs[index + 1].focus();
            }

            updateHiddenInput();
        });

        // Backspace to previous
        input.addEventListener('keydown', function (e) {
            if (e.key === 'Backspace' && input.value === '' && index > 0) {
                inputs[index - 1].focus();
            }
        });

        // Allow pasting full code
        input.addEventListener('paste', function (e) {
            e.preventDefault();
            const paste = e.clipboardData.getData('text').replace(/\D/g, ''); // numeric only

            paste.split('').forEach((char, i) => {
                if (i < inputs.length) {
                    inputs[i].value = char;
                }
            });

            updateHiddenInput();

            const next = Math.min(paste.length, inputs.length - 1);
            inputs[next].focus();
        });
    });

    function updateHiddenInput() {
        let code = '';
        inputs.forEach(i => code += i.value);
        hiddenInput.value = code;
    }

    window.addEventListener('load', () => {
        inputs[0].focus();
    });

    // Submit state
    document.getElementById('verify-form').addEventListener('submit', function () {
        const button = document.getElementById('verify-btn');
        const spinner = document.getElementById('loading-spinner');
        const btnText = document.getElementById('btn-text');

        button.disabled = true;
        spinner.classList.remove('d-none');
        btnText.textContent = 'Processing...';
    });

    function useRecoveryCode() {
        $('#recoveryModal').modal('show');
        $('#recovery-form input[name="code"]').focus();
    }

    document.getElementById('recovery-form').addEventListener('submit', function (e) {
        e.preventDefault();

        const button = document.getElementById('recovery-submit-btn');
        const spinner = document.getElementById('recovery-spinner');
        const btnText = document.getElementById('recovery-btn-text');
        const code = this.code.value;

        // Reset
        button.disabled = true;
        spinner.classList.remove('d-none');
        btnText.textContent = 'Processing...';

        // Hapus error sebelumnya
        document.querySelector('#recovery-form .alert-danger')?.remove();

        axios.post("{{ route('2fa.verify.login') }}", {
            code: code,
            source: 'recovery',
            _method: 'POST',
            _token: "{{ csrf_token() }}"
        }, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        }).then(response => {
            const successHtml = `
            <div class="alert alert-success mt-3">Recovery code verified successfully!</div>
            `;
            document.querySelector('#recovery-form .modal-body').insertAdjacentHTML('beforeend', successHtml);
            
            setTimeout(() => {
            window.location.href = response.data.redirect;
            }, 1000);
        }).catch(error => {
            // Tampilkan error di dalam modal
            let msg = 'Something went wrong.';

            if (error.response?.data?.errors?.code) {
                msg = error.response.data.errors.code[0];
            } else if (error.response?.data?.message) {
                msg = error.response.data.message;
            }

            const errorHtml = `
                <div class="alert alert-danger mt-3">${msg}</div>
            `;
            document.querySelector('#recovery-form .modal-body').insertAdjacentHTML('beforeend', errorHtml);

            // Reset tombol
            button.disabled = false;
            spinner.classList.add('d-none');
            btnText.textContent = 'Verify Recovery Code';
        });
    });


    function logout() {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, logout!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: "POST",
                    url: "{{ route('logout') }}",
                    data: {
                        "_token": "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        showCustomAlert('success', 'Logout successful!');
                        setTimeout(function() {
                            window.location.href = "{{ route('login') }}";
                        }, 1000);
                    },
                    error: function(xhr, status, error) {
                        showCustomAlert('danger', 'Logout failed! Please try again.');
                    }
                });
            }
        })
    }
</script>
@endpush