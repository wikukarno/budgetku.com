@extends('layouts.app')

@section('title', 'Tagihan - Bulanan & Tahunan')

@section('content')
<div class="row">
    <div class="col-12 col-lg-12">
        <div class="card">
            <div class="card-header d-lg-flex d-md-flex align-items-center justify-content-between">
                <h4>Daftar Tagihan Bulanan & Tahunan</h4>
                <div class="form-group">
                    <a href="{{ route('bill.create') }}" class="btn btn-primary mt-3"><i
                            class="fa-solid fa-circle-plus"></i>
                        &nbsp;
                        Tambah Data</a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="tb_bill" class="table table-hover scroll-horizontal-vertical w-100">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nama</th>
                                <th>Harga</th>
                                <th>Siklus</th>
                                <th>Jatuh Tempo</th>
                                <th>Metode Pembayaran</th>
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
@endsection



@push('after-scripts')
@include('admin.bill.components.script')
@endpush

@push('after-styles')
@include('admin.bill.components.style')
@endpush