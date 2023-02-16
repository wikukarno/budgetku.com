@extends('layouts.app')

@section('title', 'Edit Tagihan - Bulanan & Tahunan')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4>Edit Tagihan - Bulanan / Tahunan</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('bill.update', $item->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-12 col-lg-6">
                            <div class="form-group">
                                <label for="pemilik_tagihan">Pemilik Tagihan</label>
                                <input class="form-control" type="text" name="pemilik_tagihan" id="pemilik_tagihan"
                                    value="{{ $item->pemilik_tagihan }}">
                            </div>
                        </div>
                        <div class="col-12 col-lg-6">
                            <div class="form-group">
                                <label for="nama_tagihan">Nama Tagihan</label>
                                <input class="form-control" type="text" name="nama_tagihan" id="nama_tagihan"
                                    value="{{ $item->nama_tagihan }}">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col-lg-12">
                            <div class="form-group">
                                <label for="harga_tagihan">Harga Tagihan</label>
                                <input class="form-control" type="text" name="harga_tagihan" id="harga_tagihan"
                                    value="{{ $item->harga_tagihan }}">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col-lg-4">
                            <div class="form-group">
                                <label for="siklus_tagihan">Siklus Tagihan</label>
                                <select class="form-select" name="siklus_tagihan" id="siklus_tagihan">
                                    <option value="0" {{ $item->siklus_tagihan == 0 ? 'selected' : '' }}>Bulanan
                                    </option>
                                    <option value="1" {{ $item->siklus_tagihan == 1 ? 'selected' : '' }}>Tahunan
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12 col-lg-4">
                            <div class="form-group">
                                <label for="jatuh_tempo_tagihan">Jatuh Tempo Tagihan</label>
                                <input class="form-control" type="date" name="jatuh_tempo_tagihan"
                                    id="jatuh_tempo_tagihan" value="{{ \Carbon\Carbon::parse($item->jatuh_tempo_tagihan)->isoFormat(
                                    'YYYY-MM-DD') }}">
                            </div>
                        </div>
                        <div class="col-12 col-lg-4">
                            <div class="form-group">
                                <label for="metode_pembayaran">Metode Pembayaran</label>
                                <select class="form-select" name="metode_pembayaran" id="metode_pembayaran">
                                    <option value="0" {{ $item->metode_pembayaran == 0 ? 'selected' : '' }}>Cash
                                    </option>
                                    <option value="1" {{ $item->metode_pembayaran == 1 ? 'selected' : '' }}>Transfer
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col-lg-12">
                            <div class="form-group">
                                <label for="keterangan_tagihan">Keterangan Tagihan</label>
                                <textarea class="form-control" name="keterangan_tagihan" id="keterangan_tagihan">
                                    {{ $item->keterangan_tagihan }}
                                </textarea>
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
@endpush

@push('after-styles')
@endpush