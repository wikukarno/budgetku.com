@extends('layouts.app')

@section('title', 'Uang Masuk')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('salary.update', $data->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="container">
                        <div class="row">
                            <div class="col-12 col-lg-6">
                                <div class="form-group">
                                    <label for="salary">Uang Masuk</label>
                                    <input type="text" name="salary" id="salary" class="form-control" value="Rp. {{ number_format($data->salary, 0, ',', '.') }}"
                                        required>
                                </div>
                            </div>
                            <div class="col-12 col-lg-6">
                                <div class="form-group">
                                    <label for="date">Tanggal</label>
                                    <input type="text" name="date" id="date" value="{{ $data->date }}" class="form-control" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 col-lg-12">
                                <div class="form-group">
                                    <label for="description">Keterangan</label>
                                    <input type="text" name="description" id="description" class="form-control" value="{{ $data->description }}" required>
                                </div>
                            </div>
                        </div>
                        <div class="form-group text-end mt-5">
                            <a href="{{ route('salary.index') }}" type="button" class="btn btn-secondary">Batal</a>
                            <button type="submit" id="btnSaveSalary" class="btn btn-primary">Simpan</button>
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

    var rupiah = document.getElementById('salary');
    rupiah.addEventListener('keyup', function(e){
        rupiah.value = formatRupiah(this.value, 'Rp. ');
    });
</script>
@endpush

@push('after-styles')
@endpush