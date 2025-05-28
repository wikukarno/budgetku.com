@extends('layouts.v2.app')

@section('title', 'Help Center')

@section('content')
<div class="card bg-white border-0 rounded-3 mb-4">
    <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
            <h3 class="mb-0">
                Help Center
            </h3>
        </div>

        <form class="needs-validation" method="POST" action="{{ route('customer.help.center.send') }}" novalidate>
            @csrf
            <div class="row">
                <div class="col-lg-6 col-sm-6">
                    <div class="form-group mb-4">
                        <label class="label text-secondary">Name</label>
                        <input type="text" class="form-control h-55" name="name" value="{{ Auth::user()->name }}" placeholder="Enter name">
                    </div>
                </div>
                <div class="col-lg-6 col-sm-6">
                    <div class="form-group mb-4">
                        <label class="label text-secondary">Email</label>
                        <input type="email" class="form-control h-55" name="email" value="{{ Auth::user()->email }}" placeholder="Enter email">
                    </div>
                </div>
                
                <div class="col-lg-12">
                    <div class="form-group mb-4">
                        <label class="label text-secondary fs-14">
                            Message
                        </label>

                        <textarea rows="5" class="form-control" style="height: 170px;" name="message" placeholder="Type your message here..." required></textarea>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="d-flex flex-wrap justify-content-end gap-3">
                        <a href="{{ route('customer.dashboard') }}" class="btn btn-danger py-2 px-4 fw-medium fs-16 text-white">Cancel</a>
                        <button type="submit" class="btn btn-primary py-2 px-4 fw-medium fs-16"> 
                            <i class="ri-send-plane-2-line text-white fw-medium"></i>
                            Send Now
                            </button>
                    </div>
                </div>
            </div>
        </form>

        <div class="cf-turnstile" data-sitekey="{{ env('TURNSTILE_SITE') }}"></div>
        <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
    </div>
</div>

@endsection

@push('after-scripts')
<script>
    
</script>
@endpush