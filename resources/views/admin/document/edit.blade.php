@extends('layouts.app')

@section('title', 'Edit Tentang')

@section('content')
<div class="row add-about-section">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4>Edit Tentang Saya</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('document.update', $item->id) }}" method="post" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-12 col-lg-6">
                            <div class="form-group">
                                <label for="name">Nama</label>
                                <input type="text" name="name" value="{{ $item->name }}" id="name" class="form-control">
                            </div>
                        </div>
                        <div class="col-12 col-lg-6">
                            <div class="form-group">
                                <label for="file">File</label>
                                <input type="file" name="file" id="file" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex d-grid gap-2">
                        <a href="{{ route('about.index') }}" class="btn btn-danger col-6">Batal</a>
                        <button class="btn btn-primary col-6" type="submit">Simpan</button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('after-scripts')
<style>
    .ck-editor__editable_inline {
        min-height: 100px;
    }
</style>
@endpush