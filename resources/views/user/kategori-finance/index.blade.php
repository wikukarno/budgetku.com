@extends('layouts.app')

@section('title', 'Kategori Keuangan')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-lg-flex d-md-flex align-items-center justify-content-between">
                <h4>Kategori Keuangan</h4>
                <div class="form-group">
                    <a href="javascript:void(0)" class="btn btn-primary mt-3" onclick="addCategoryFinance();"><i
                            class="fa-solid fa-circle-plus"></i>
                        &nbsp;
                        Tambah Kategori</a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="tb_category_finance" class="table table-hover scroll-horizontal-vertical w-100">
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
@include('admin.kategori-finance.components.modal-kategori-finance')
@endsection


@push('after-scripts')
@include('admin.kategori-finance.components.script')
@endpush

@push('after-styles')
@include('admin.kategori-finance.components.style')
@endpush