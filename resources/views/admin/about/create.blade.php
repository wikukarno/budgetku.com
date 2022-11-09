@extends('layouts.app')

@section('title', 'Tambah Tentang')

@section('content')
<div class="row add-about-section">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4>Tambah Tentang Saya</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('about.store') }}" method="post">
                    @csrf
                    <div class="form-group">
                        <textarea class="form-control" rows="20" name="description"></textarea>
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