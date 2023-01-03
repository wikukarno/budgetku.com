@extends('layouts.app')

@section('title', 'Keuangan')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-lg-flex d-md-flex align-items-center justify-content-between">
                <h4>Keuangan</h4>
                <div class="form-group">
                    <a href="javascript:void(0)" class="btn btn-primary mt-3" onclick="addFinance();"><i
                            class="fa-solid fa-circle-plus"></i>
                        &nbsp;
                        Tambah Data</a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="tb_finance" class="table table-hover scroll-horizontal-vertical w-100">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Kategori</th>
                                <th>Nama Barang / Item</th>
                                <th>Harga</th>
                                <th>Metode Pembayaran</th>
                                <th>Tanggal Pembelian</th>
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
@include('admin.finance.components.modal-finance')
@endsection



@push('after-scripts')
@include('admin.finance.components.script')
@endpush

@push('after-styles')
@include('admin.finance.components.style')
@endpush