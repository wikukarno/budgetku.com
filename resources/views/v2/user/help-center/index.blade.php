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
                        <label class="label text-secondary fs-14">Message</label>
                        <textarea rows="5" class="form-control" style="height: 170px;" name="message"
                            placeholder="Type your message here..." required></textarea>
                    </div>
                </div>

                <!-- Cloudflare Turnstile -->
                <div class="cf-turnstile mb-3" data-sitekey="{{ env('TURNSTILE_SITE') }}"></div>
                <input type="hidden" name="cf-turnstile-response" id="cf-turnstile-response">

                <div class="col-lg-12">
                    <div class="d-flex flex-wrap justify-content-end gap-3">
                        <a href="{{ route('customer.dashboard') }}"
                            class="btn btn-danger py-2 px-4 fw-medium fs-16 text-white">Cancel</a>
                        <button id="btnSend" class="btn btn-primary py-2 px-4 fw-medium fs-16" type="submit">
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
<script>
    // Render CAPTCHA dan isi token ke hidden input
    window.onloadTurnstileCallback = function () {
        turnstile.render('.cf-turnstile', {
            sitekey: "{{ env('TURNSTILE_SITE') }}",
            callback: function(token) {
                document.getElementById('cf-turnstile-response').value = token;
            }
        });
    };
</script>
<script src="https://challenges.cloudflare.com/turnstile/v0/api.js?onload=onloadTurnstileCallback" async defer></script>

<script>
    document.getElementById('btnSend').addEventListener('click', function(event) {
        event.preventDefault();
        const form = document.getElementById('formdata');
        const captchaValue = document.getElementById('cf-turnstile-response').value;

        if (!captchaValue) {
            Swal.fire({
                icon: 'warning',
                title: 'Validation Error',
                text: 'Please complete the CAPTCHA verification.',
            });
            return;
        }

        const formData = new FormData(form);
        formData.set('cf-turnstile-response', captchaValue);

        fetch("{{ route('customer.help.center.send') }}", {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: data.message,
                }).then(() => {
                    window.location.href = "{{ route('customer.dashboard') }}";
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.message,
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'An error occurred while sending your message.',
            });
        });
    });
</script>
@endpush