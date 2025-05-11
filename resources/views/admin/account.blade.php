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

                <form action="{{ route('user.ubah.profile') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id" value="{{ Auth::user()->id }}">
                    <div class="text-center thumbnail-image" onclick="updateImage()">
                        @if (Auth::user()->avatar != null)
                        <img src="{{ Storage::url(Auth::user()->avatar) }}" class="figure-img img-fluid rounded-circle"
                            alt="foto profile" id="foto-profile" style="max-height: 150px; background-size: cover" />
                        <input type="file" name="avatar" id="update-image-user" style="display: none"
                            onchange="this.form.submit()">
                        @else
                        <img class="profile-user-img img-fluid img-circle"
                            src="{{ asset('assets/images/faces/face1.jpg') }}" alt="User profile picture"
                            style="max-height: 150px">
                        <input type="file" name="avatar" id="update-image-user" style="display: none"
                            onchange="this.form.submit()">
                        @endif
                    </div>
                </form>

                <form action="{{ route('account.update', $user->id) }}" method="POST" id="update-akun">
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
                    <div class="row">
                        <div class="col-12 col-lg-12">
                            <div class="form-group">
                                <label for="email_parrent">Email Orang Tua</label>
                                <input type="text" name="email_parrent" id="email_parrent" class="form-control"
                                    value="{{ $user->email_parrent }}">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label for="notifications" class="form-label">Notifikasi Email</label>
                                <select name="notifications" id="notifications" class="form-select">
                                    <option value="1" {{ $user->notifications == 1 ? 'selected' : '' }}>Aktif</option>
                                    <option value="0" {{ $user->notifications == 0 ? 'selected' : '' }}>Tidak Aktif
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col-lg-12">
                            <div class="form-group">
                                <ul>
                                    <li>
                                        Masukkan email orang tua dengan format yang benar dan pisahkan dengan koma. <br>
                                        Contoh: bapak@gmail.com, ibu@gmail.com
                                    </li>
                                    <li>
                                        Email orang tua digunakan untuk mengirimkan informasi terkait manajemen
                                        keuangan.
                                    </li>
                                    <li>
                                        Maksimal 2 email orang tua.
                                    </li>
                                    <li>
                                        Klik gambar di atas untuk mengganti foto profile.
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="d-block d-lg-flex d-grid gap-2">
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-danger col-12 col-lg-6">Batal</a>
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
<script src="https://cdn.jsdelivr.net/npm/@yaireo/tagify"></script>
<script src="https://cdn.jsdelivr.net/npm/@yaireo/tagify/dist/tagify.polyfills.min.js"></script>
<script>
    function updateImage() {
        document.getElementById('update-image-user').click();
    }

    // Inisialisasi Tagify pada input email orang tua
    var input = document.getElementById('email_parrent');
    var tagify = new Tagify(input, {
        delimiters: ",",  // Set delimiters untuk memisahkan email dengan koma
        pattern: /^[^\s@]+@[^\s@]+\.[^\s@]+$/,  // Validasi format email
    });

    // Tangkap event submit form
    $('#update-akun').submit(function (e) {
        e.preventDefault();  // Mencegah reload halaman

        // Ambil nilai dari Tagify (array email)
        let emails = tagify.value.map(item => item.value);

        // Masukkan email yang sudah dipisah koma ke dalam input hidden atau formData
        var formData = new FormData(this);
        formData.set('email_parrent', emails.join(','));  // Gabungkan array email dengan koma

        // Submit form via AJAX
        $.ajax({
            url: this.action,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: 'Data berhasil diperbarui.',
                    showConfirmButton: true
                    
                });
            },
            error: function (xhr, status, error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: 'Terjadi kesalahan, mohon coba lagi.',
                    showConfirmButton: true
                });
            }
        });
    });
</script>
@endpush

@push('after-styles')
<link href="https://cdn.jsdelivr.net/npm/@yaireo/tagify/dist/tagify.css" rel="stylesheet" type="text/css" />
<style>
    .profile-user-img {
        max-width: 150px;
        max-height: 150px;
        border-radius: 50%;
        background-size: cover;
    }

    img {
        cursor: pointer;
    }
</style>
@endpush