@extends('layouts.app')

@section('title', 'Tambah Uang Keluar')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('finance.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="container">
                        <div class="row">
                            <div class="col-12 col-lg-6">
                                <div class="form-group">
                                    <label for="name">Nama Kategori</label>
                                    <select name="category_finances_id" id="category_finances_id" class="form-select" required>
                                        <option value="">Pilih Kategori</option>
                                        @foreach ($categories as $item)
                                        <option value="{{ $item->id }}">
                                            {{ $item->name_category_finances }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-lg-6">
                                <div class="form-group">
                                    <label for="name">Nama Barang / Item</label>
                                    <input type="text" name="name_item" id="name_item" class="form-control" placeholder="Makanan"
                                        required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 col-lg-4">
                                <div class="form-group">
                                    <label for="name">Harga Barang / Item</label>
                                    <input type="text" name="price" id="price" class="form-control" placeholder="Rp.100.000" required>
                                </div>
                            </div>
                            <div class="col-12 col-lg-4">
                                <div class="form-group">
                                    <label for="purchase_date">Tanggal Bayar</label>
                                    <input type="text" name="purchase_date" id="date" class="form-control">
                                </div>
                            </div>
                            <div class="col-12 col-lg-4">
                                <div class="form-group">
                                    <label for="name">Metode Pembayaran</label>
                                    <input type="text" name="purchase_by" id="purchase_by" class="form-control"
                                        placeholder="Tunai, gopay, transfer, dll">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 col-lg-12">
                                <div class="form-group">
                                    <label for="bukti_pembayaran">
                                        File Bukti Pembayaran (opsional)
                                    </label>
                                    <input type="file" class="form-control" name="bukti_pembayaran" id="bukti_pembayaran">
                                </div>
                            </div>
                        </div>
                        <div class="form-group text-end mt-5">
                            <a href="{{ route('finance.index') }}" class="btn btn-secondary">Batal</a>
                            <button type="submit" id="btnSaveKeuangan" class="btn btn-primary">Simpan</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection



@push('after-scripts')
<script>

    var selectKategori = document.getElementById('category_finances_id');
    var choices = new Choices(selectKategori, {
        searchEnabled: true,
        itemSelectText: '',
    });

    function formatRupiah(angka, prefix){
        var number_string = angka.replace(/[^,\d]/g, '').toString(),
            split   		= number_string.split(','),
            sisa     		= split[0].length % 3,
            rupiah     		= split[0].substr(0, sisa),
            ribuan     		= split[0].substr(sisa).match(/\d{3}/gi);
        
        if(ribuan){
            separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }

        rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
        return prefix == undefined ? rupiah : (rupiah ? 'Rp. ' + rupiah : '');
    }

    var rupiah = document.getElementById('price');
    rupiah.addEventListener('keyup', function(e){
        rupiah.value = formatRupiah(this.value, 'Rp. ');
    });
</script>
@endpush

@push('after-styles')
@endpush