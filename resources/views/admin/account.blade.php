@extends('layouts.app')

@section('title', 'Akun Saya')

@section('content')
<div class="row">
    <div class="col-12 col-lg-12">
        <div class="card">
            <div class="card-header">
                <h5>Akun Saya</h5>
            </div>
            <div class="card-body">

                <form action="{{ route('ubah-profile') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id" value="{{ Auth::user()->id }}">
                    <div class="text-center thumbnail-image" onclick="updateImage()">
                        @if (Auth::user()->avatar != null)

                        <img src="{{ Storage::url(Auth::user()->avatar) }}" class="figure-img img-fluid rounded-circle"
                            alt="foto profile" id="foto-profile" style="max-height: 150px; background-size: cover" />

                        <input type="file" name="avatar" id="update-image-user" style="display: none"
                            onchange="form.submit()">

                        @else

                        <img class="profile-user-img img-fluid img-circle" src="{{ asset('assets/img/avatar.png') }}"
                            alt="User profile picture" style="max-height: 150px">
                        <input type="file" name="avatar" id="update-image-user" style="display: none"
                            onchange="form.submit()">

                        @endif
                    </div>
                </form>

                <form action="{{ route('account.update', $user->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-12 col-lg-4">
                            <div class="form-group">
                                <label for="name">Nama</label>
                                <input type="text" name="name" id="name" class="form-control" value="{{ $user->name }}">
                            </div>
                        </div>
                        <div class="col-12 col-lg-4">
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" name="email" id="email" class="form-control"
                                    value="{{ $user->email }}">
                            </div>
                        </div>
                        <div class="col-12 col-lg-4">
                            <div class="form-group">
                                <label for="roles">Status</label>
                                <input type="roles" name="roles" id="roles" class="form-control" readonly
                                    value="{{ $user->roles }}">
                            </div>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="d-block d-lg-flex d-grid gap-2">
                            <a href="{{ route('dashboard') }}" class="btn btn-danger col-12 col-lg-6">Batal</a>
                            <button class="btn btn-primary col-12 col-lg-6" type="submit">Simpan</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('after-scripts')
<script>
    function updateImage() {
            document.getElementById('update-image-user').click();
        }

</script>
@endpush

@push('after-styles')
<style>
    img {
        cursor: pointer;
    }
</style>