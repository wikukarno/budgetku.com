@extends('layouts.app')

@section('title', 'Keuangan')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-lg-flex d-md-flex align-items-center justify-content-between">
                <h4>Keuangan</h4>
                <div class="form-group">
                    <a href="{{ route('finance.create') }}" class="btn btn-primary mt-3" onclick="addFinance();"><i
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
                                <th>Nama Item</th>
                                <th>Harga</th>
                                <th>Tanggal</th>
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
<script>
    function deleteFinance(id){
        Swal.fire({
            title: 'Apakah anda yakin?',
            text: "Data yang dihapus tidak dapat dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, hapus data!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type:"DELETE",
                    url: "{{ route('delete-finance') }}",
                    data: {
                        "_token": "{{ csrf_token() }}",
                        id:id
                    },
                    dataType: 'json',
                    beforeSend: function() {
                        $(".preloader").fadeIn();
                    },
                    success: function(res){
                        $('#tb_finance').DataTable().ajax.reload();
                        if(res.code == 200){
                            Swal.fire({
                                title: 'Berhasil!',
                                text: res.message,
                                icon: 'success',
                                confirmButtonText: 'Ok'
                            })
                        }else{
                            Swal.fire({
                                title: 'Gagal!',
                                text: res.message,
                                icon: 'error',
                                confirmButtonText: 'Ok'
                            })
                        }
                    },
                    complete: function(){
                        $(".preloader").fadeOut();
                    }
                });
            }
        })
    }

    $('#tb_finance').dataTable({
        processing: true,
        serverSide: true,
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
        dom: 'lBfrtip',
        buttons: [
        'copy', 'csv', 'excel', 'pdf', 'print',
        ],
        order : [[5, 'desc']],
        ajax: {
            type: 'GET',
            url: "{{ route('finance.index') }}",
        },
        columns: [
            { data: 'DT_RowIndex', name: 'id'},
            { data: 'category_finance.name_category_finances', name: 'category_finance.name_category_finances' },
            { data: 'name_item', name: 'name_item' },
            { data: 'price', name: 'price' },
            { data: 'purchase_date', name: 'purchase_date' },
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
@include('admin.finance.components.style')
@endpush