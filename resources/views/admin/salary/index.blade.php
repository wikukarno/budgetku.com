@extends('layouts.app')

@section('title', 'Uang Masuk')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-lg-flex d-md-flex align-items-center justify-content-between">
                <h4>Daftar Uang Masuk</h4>
                <div class="form-group">
                    <a href="javascript:void(0)" class="btn btn-primary mt-3" onclick="addSalary();"><i
                            class="fa-solid fa-circle-plus"></i>
                        &nbsp;
                        Tambah Data</a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="tb_salary" class="table table-hover scroll-horizontal-vertical w-100">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Uang Masuk</th>
                                <th>Tanggal</th>
                                <th>Keterangan</th>
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
@include('admin.salary.components.modal-salary')
@endsection



@push('after-scripts')
@include('admin.salary.components.script')
@endpush

@push('after-styles')
@include('admin.salary.components.style')
@endpush