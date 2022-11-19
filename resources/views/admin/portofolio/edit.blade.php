@extends('layouts.app')

@section('title', 'Edit Tentang')

@section('content')
<div class="row add-about-section">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4>Edit Portofolio</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('portofolio.update', $portofolio->id) }}" method="post"
                    enctype="multipart/form-data">
                    @method('PUT')
                    @csrf
                    <div class="row">
                        <div class="col-4">
                            <div class="form-group">
                                <label for="title" class="form-label">Nama Portofolio</label>
                                <input type="text" class="form-control" id="title" value="{{ $portofolio->title }}"
                                    name="title" placeholder="Nama Portofolio">
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                <label for="kategori" class="form-label">Nama Portofolio</label>
                                <select class="form-select" name="kategori" aria-label="Default select example">
                                    <option selected>{{ $portofolio->kategori }}</option>
                                    <option value="Web Design">Web Design</option>
                                    <option value="Web Design / Code">Web Design / Code</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                <label for="published" class="form-label">Tanggal Dibuat</label>
                                <input type="date" class="form-control" name="published"
                                    value="{{ $portofolio->published }}">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label for="url" class="form-label">Url</label>
                                <input type="url" class="form-control" id="url" value="{{ $portofolio->url }}"
                                    name="url" placeholder="Masukan Url">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label for="url" class="form-label">Thumbnail</label>
                                <input type="file" class="form-control" id="thumbnail" name="thumbnail" accept="image/*"
                                    onchange="validateSize()">
                            </div>
                        </div>
                    </div>
                    <div class="d-flex d-grid gap-2 mt-3">
                        <a href="{{ route('portofolio.index') }}" class="btn btn-danger col-6">Batal</a>
                        <button type="submit" class="btn btn-primary col-6">Simpan</button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('after-scripts')
<script>
    validateSize = () => {
            const fi = document.getElementById('thumbnail');

            if (fi.files.length > 0) {
                for (const i = 0; i <= fi.files.length - 1; i++) { 
                    const fsize=fi.files.item(i).size; 
                    const file=Math.round((fsize / 1024)); // The size of the file. 
                    
                    if (file >= 2096) {
                        alert("File too Big, please select a file less than 4mb");
                    } else {
                    document.getElementById('size').innerHTML='<b>' + file + '</b> KB' ; 
                    } 
                } 
            }
        } 
</script>
@endpush

@push('after-styles')
<style>
    .ck-editor__editable_inline {
        min-height: 100px;
    }
</style>
@endpush