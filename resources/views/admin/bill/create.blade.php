@extends('layouts.app')

@section('title', 'Tagihan - Bulanan & Tahunan')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4>Tambah Tagihan - Bulanan / Tahunan</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('bill.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-12 col-lg-12">
                            <div class="form-group">
                                <label for="nama_tagihan">Nama Tagihan</label>
                                <input class="form-control" type="text" name="nama_tagihan" id="nama_tagihan" placeholder="Pajak Kendaraan">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col-lg-12">
                            <div class="form-group">
                                <label for="harga_tagihan">Harga Tagihan</label>
                                <input class="form-control" type="text" name="harga_tagihan" id="harga_tagihan" placeholder="Rp. 1.000.000">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col-lg-4">
                            <div class="form-group">
                                <label for="siklus_tagihan">Siklus Tagihan</label>
                                <select class="form-select" name="siklus_tagihan" id="siklus_tagihan">
                                    <option value="0">Bulanan</option>
                                    <option value="1">Tahunan</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12 col-lg-4">
                            <div class="form-group">
                                <label for="jatuh_tempo_tagihan">Jatuh Tempo Tagihan</label>
                                <input class="form-control" type="text" name="jatuh_tempo_tagihan" id="date">
                            </div>
                        </div>
                        <div class="col-12 col-lg-4">
                            <div class="form-group">
                                <label for="metode_pembayaran">Metode Pembayaran</label>
                                <select class="form-select" name="metode_pembayaran" id="metode_pembayaran">
                                    <option value="0">Cash</option>
                                    <option value="1">Transfer</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col-lg-12">
                            <div class="form-group">
                                <label for="keterangan_tagihan">Keterangan Tagihan</label>
                                <textarea class="form-control" name="keterangan_tagihan" id="keterangan_tagihan"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex d-block gap-2 mt-5">
                            <a href="{{ route('bill.index') }}" class="btn btn-secondary col-6">Batal</a>
                            <button type="submit" class="btn btn-primary col-6">Simpan</button>
                    </div>
                </form>
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