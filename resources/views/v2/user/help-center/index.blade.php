@extends('layouts.v2.app')

@section('title', 'Help Center')

@section('content')
<div class="card bg-white border-0 rounded-3 mb-4">
    <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
            <h3 class="mb-0">Help Center</h3>
        </div>

        <form method="POST" id="formdata">
            @csrf
            <div class="row">
                <div class="col-lg-6 col-sm-6">
                    <div class="form-group mb-4">
                        <label class="label text-secondary">Name</label>
                        <input type="text" class="form-control h-55" name="name" value="{{ Auth::user()->name }}"
                            placeholder="Enter name" required>
                    </div>
                </div>
                <div class="col-lg-6 col-sm-6">
                    <div class="form-group mb-4">
                        <label class="label text-secondary">Email</label>
                        <input type="email" class="form-control h-55" name="email" value="{{ Auth::user()->email }}"
                            placeholder="Enter email" required>
                    </div>
                </div>

                <div class="col-lg-12">
                    <div class="form-group mb-4">
                        <label class="label text-secondary">Message</label>
                        <textarea class="form-control" name="message" rows="5" placeholder="Type your message here..."
                            required></textarea>
                    </div>
                </div>

                <!-- Cloudflare Turnstile CAPTCHA -->
                <div class="col-lg-12 mb-3">
                    <div class="cf-turnstile" data-sitekey="{{ env('TURNSTILE_SITE') }}"
                        data-callback="turnstileCallback">
                    </div>
                    <input type="hidden" name="cf-turnstile-response" id="cf-turnstile-response">
                </div>

                <div class="col-lg-12">
                    <div class="d-flex flex-wrap justify-content-end gap-3">
                        <a href="{{ route('customer.dashboard') }}"
                            class="btn btn-danger py-2 px-4 fw-medium fs-16 text-white">Cancel</a>
                        <button id="btnSend" type="submit" class="btn btn-primary py-2 px-4 fw-medium fs-16">
                            <i class="ri-send-plane-2-line text-white fw-medium"></i>
                            Send Now
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('after-scripts')
<script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>

<script>
    // Auto-fill hidden input when CAPTCHA is verified
    function turnstileCallback(token) {
        document.getElementById('cf-turnstile-response').value = token;
    }

    document.getElementById('btnSend').addEventListener('click', function (e) {
        e.preventDefault();

        const form = document.getElementById('formdata');
        const token = document.getElementById('cf-turnstile-response').value;

        if (!token) {
            Swal.fire({
                icon: 'warning',
                title: 'Validation Error',
                text: 'Please complete the CAPTCHA verification.',
            });
            return;
        }

        const formData = new FormData(form);

        fetch("{{ route('customer.help.center.send') }}", {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            Swal.fire({
                icon: data.success ? 'success' : 'error',
                title: data.success ? 'Success' : 'Error',
                text: data.message
            }).then(() => {
                if (data.success) {
                    window.location.href = "{{ route('customer.dashboard') }}";
                }
            });
        })
        .catch(() => {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'An error occurred. Please try again.'
            });
        });
    });
</script>
@endpush