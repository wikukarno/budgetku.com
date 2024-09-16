@extends('layouts.auth')

@section('title', 'Aplikasi KeuanganKu')

@section('content')
<div class="container-scroller">
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
                        </form>
                    </div>
                </div>
            </div>
            <!-- content-wrapper ends -->
        </div>
        <!-- row ends -->
    </div>
    <!-- page-body-wrapper ends -->
</div>

@endsection