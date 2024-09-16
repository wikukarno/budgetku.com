@extends('layouts.app')

@section('title', 'Keuangan')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-lg-flex d-md-flex align-items-center justify-content-between">
                <h4>Daftar Uang Keluar</h4>
                <div class="form-group">
                    <a href="{{ route('expense.create') }}" class="btn btn-primary mt-3" onclick="addexpense();"><i
                            class="fa-solid fa-circle-plus"></i>
                        &nbsp;
                        Tambah Data</a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="tb_expense" class="table table-hover scroll-horizontal-vertical w-100">
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
@include('user.expense.components.modal-expense')
@endsection



@push('after-scripts')
<script>
    function deleteExpense(id){
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
                    url: "{{ route('expense.destroy') }}",
                    data: {
                        "_token": "{{ csrf_token() }}",
                        id:id
                    },
                    dataType: 'json',
                    beforeSend: function() {
                        $(".preloader").fadeIn();
                    },
                    success: function(res){
                        $('#tb_expense').DataTable().ajax.reload();
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

    $('#tb_expense').dataTable({
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
            url: "{{ route('expense.index') }}",
        },
        columns: [
            { data: 'DT_RowIndex', name: 'id'},
            { data: 'category_finances_id', name: 'category_finances_id' },
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
@include('user.expense.components.style')
@endpush