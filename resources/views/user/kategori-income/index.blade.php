@extends('layouts.app')

@section('title', 'Kategori Uang Masuk')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-lg-flex d-md-flex align-items-center justify-content-between">
                <h4>Kategori Uang Masuk</h4>
                <div class="form-group">
                    <a href="javascript:void(0)" class="btn btn-primary mt-3" onclick="addCategoryIncome();"><i
                            class="fa-solid fa-circle-plus"></i>
                        &nbsp;
                        Tambah Kategori</a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="tb_kategori_uang_masuk" class="table table-hover scroll-horizontal-vertical w-100">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nama</th>
                                <th>Tanggal Dibuat</th>
                                <th>Tanggal Diperbarui</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@include('user.kategori-income.components.modal-kategori-income')
@endsection


@push('after-scripts')
@include('user.kategori-income.components.script')
@endpush

@push('after-styles')
@include('user.kategori-income.components.style')
@endpush