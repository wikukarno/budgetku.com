@extends('layouts.app')

@section('title', 'Uang Masuk')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-lg-flex d-md-flex align-items-center justify-content-between">
                <h4>Daftar Uang Masuk</h4>
                <div class="form-group">
                    <a href="{{ route('income.create') }}" class="btn btn-primary mt-3" onclick="addSalary();"><i
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
                                <th>Tipe</th>
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
@endsection



@push('after-scripts')
<script>
    function deleteSalary(id){
        Swal.fire({
            title: 'Apakah anda yakin?',
            text: "Data ini akan dihapus!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, hapus data!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type:"DELETE",
                    url: "{{ route('income.destroy') }}",
                    data: {
                        "_token": "{{ csrf_token() }}",
                        id:id
                    },
                    dataType: 'json',
                    beforeSend: function() {
                        $(".preloader").fadeIn();
                    },
                    success: function(res){
                        $('#tb_salary').DataTable().ajax.reload();
                        if(res.code == 200){
                            Swal.fire({
                                title: 'Berhasil!',
                                text: 'Data berhasil dihapus.',
                                icon: 'success',
                            });
                        }else{
                            Swal.fire({
                                title: 'Gagal!',
                                text: 'Data gagal dihapus.',
                                icon: 'error',
                            });
                        }
                    },
                    error: function(xhr){
                        Swal.fire({
                            title: 'Gagal!',
                            text: 'Data gagal dihapus.',
                            icon: 'error',
                        });
                    },
                    complete: function(){
                        $(".preloader").fadeOut();
                    }
                });
            }
        })
    }

    $('#tb_salary').dataTable({
        processing: true,
        serverSide: true,
        ajax: {
            type: 'GET',
            url: "{!! url()->current() !!}"
        },
        columns: [
            { data: 'DT_RowIndex', name: 'id'},
            { data: 'tipe', name: 'tipe' },
            { data: 'salary', name: 'salary' },
            { data: 'date', name: 'date' },
            { data: 'description', name: 'description' },
            {
                data: 'action',
                searchable: false,
                sortable: false
            }
        ]
    });
</script>
@endpush

@push('after-styles')
@endpush