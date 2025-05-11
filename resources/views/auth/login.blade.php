@extends('layouts.auth')

@section('title', 'BudgetKu - Login')

@section('content')

<div class="row align-items-center">
    <div class="col-lg-6 d-none d-lg-block">
        <img src="{{ asset('banner.webp') }}" class="rounded-3" alt="login">
    </div>
    <div class="col-lg-6">
        <div class="mw-480 ms-lg-auto">
            <div class="d-inline-block mb-4">
                <img src="{{ asset('v2/images/logo.svg') }}" class="rounded-3 for-light-logo" alt="login">
                <img src="{{ asset('v2/images/logo.svg') }}" class="rounded-3 for-dark-logo" alt="login">
            </div>
            <h3 class="fs-28 mb-2">Welcome back to BudgetKu!</h3>
            <p class="fw-medium fs-16 mb-4">Sign In or Sign Up with social account or enter your details</p>
            <div class="row justify-content-start">
                <div class="col-lg-4 col-sm-4">
                    <a href="{{ url('auth/redirect') }}" class="btn btn-outline-secondary bg-transparent w-100 py-2 hover-bg mb-4"
                        style="border-color: #D6DAE1;">
                        <img src="auth/images/google.svg" alt="google">
                    </a>
                </div>
            </div>
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                        <li class="mb-1">{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form action="{{ route('login') }}" method="POST">
                @csrf
                <div class="form-group mb-4">
                    <label class="label text-secondary">Email Address</label>
                    <input type="email" name="email" class="form-control h-55" placeholder="example@budgetku.com" required>
                </div>
                <div class="form-group mb-4">
                    <label class="label text-secondary">Password</label>
                    <input type="password" name="password" class="form-control h-55" placeholder="Type password" required>
                </div>
                <div class="form-group mb-4">
                    <button type="submit" class="btn btn-primary fw-medium py-2 px-3 w-100">
                        <div class="d-flex align-items-center justify-content-center py-1">
                            <i class="material-symbols-outlined text-white fs-20 me-2">login</i>
                            <span>Login</span>
                        </div>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


{{-- <div class="container-scroller">
    <div class="container-fluid page-body-wrapper full-page-wrapper">
        <div class="row w-100 m-0">
            <div class="content-wrapper full-page-wrapper d-flex align-items-center auth login-bg">
                <div class="card col-lg-4 mx-auto">
                    <div class="card-body px-5 py-5">
                        <h3 class="card-title text-center mb-3">Login</h3>
                        <h4 class="text-center mb-5">
                            Aplikasi KeuanganKu
                        </h4>
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                    <li class="mb-1">{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <form method="POST" action="{{ route('login') }}">
                            @csrf
                            <div class="form-group">
                                <label>Username or email *</label>
                                <input type="text" name="email" class="form-control p_input">
                            </div>
                            <div class="form-group">
                                <label>Password *</label>
                                <input type="password" name="password" class="form-control p_input">
                            </div>
                            <div class="text-center d-grid mb-3">
                                <button type="submit" class="btn btn-primary btn-block enter-btn">Login</button>
                            </div>
                            <div class="d-grid mb-3">
                                <a href="{{ url('/auth/redirect') }}" class="btn btn-google btn-lg col">
                                    <i class="mdi mdi-google-plus"></i> Masuk / Daftar dengan Google </a>
                            </div>
                            @auth
                                <div class="d-grid mb-3">
                                    @if (Auth::user()->roles == 'Owner')
                                        <a href="{{ route('admin.dashboard') }}" class="btn btn-primary btn-lg col">
                                            <i class="mdi mdi-home"></i> Dashboard
                                        </a>
                                    @elseif (Auth::user()->roles == 'Customer')
                                        <a href="{{ route('customer.dashboard') }}" class="btn btn-primary btn-lg col">
                                            <i class="mdi mdi-home"></i> Dashboard
                                        </a>
                                    @endif
                                </div>
                            @endauth
                        </form>
                    </div>
                </div>
            </div>
            <!-- content-wrapper ends -->
        </div>
        <!-- row ends -->
    </div>
    <!-- page-body-wrapper ends -->
</div> --}}

@endsection