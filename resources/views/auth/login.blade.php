@extends('layouts.auth')

@section('title', 'BudgetKu - Login to your account')

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
                        <img src="{{ asset('v2/images/google.svg') }}" alt="google">
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
@endsection