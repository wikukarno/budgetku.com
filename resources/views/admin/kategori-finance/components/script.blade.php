<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

<script>
    function addCategoryFinance() {
        $('#categoryFinanceModal').modal('show');
        $('#categoryFinanceModalLabel').html('Tambah Kategori Keuangan');
        $('#id_category_finance').val('');
        $('#form-tambah-kategori-finance').trigger('reset');
        $('#btnSaveKategoriKeuangan').html('Simpan');
        $('#btnSaveKategoriKeuangan').attr('disabled', false);
    }

    function updateKategoriFinance(id){
        $('#form-tambah-kategori-finance').trigger('reset');
        $('#categoryFinanceModal').modal('show');
        $('#categoryFinanceModalLabel').html('Update Kategori Keuangan');
        $('#id_category_finance').val(id);
        $('#btnSaveKategoriKeuangan').html('Simpan Perubahan');
        $('#btnSaveKategoriKeuangan').attr('disabled', false);
        
        $.ajax({
            type:"POST",
            url: "{{ url('/pages/dashboard/show/kategori') }}",
            data: {
                "_token": "{{ csrf_token() }}",
                id:id
            },
            dataType: 'json',
            beforeSend: function() {
                $(".preloader").fadeIn();
            },
            success: function(res){
                $('#id_kategori_finance').val(res.id);
                $('#name_category_finances').val(res.name_category_finances);
            },
            complete: function(){
                $(".preloader").fadeOut();
            }
        });
    }

    function deleteKategoriFinance(id){
        Swal.fire({
            title: 'Apakah anda yakin?',
            text: "Data yang dihapus tidak dapat dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type:"POST",
                    url: "{{ url('/pages/dashboard/delete/kategori') }}",
                    data: {
                        "_token": "{{ csrf_token() }}",
                        id:id
                    },
                    dataType: 'json',
                    beforeSend: function() {
                        $(".preloader").fadeIn();
                    },
                    success: function(res){
                        $('#tb_category_finance').DataTable().ajax.reload();
                        Swal.fire(
                            'Terhapus!',
                            'Data berhasil dihapus.',
                            'success'
                        )
                    },
                    complete: function(){
                        $(".preloader").fadeOut();
                    }
                });
            }
        })
    }
    
    $('#form-tambah-kategori-finance').submit(function (e){
        e.preventDefault();
        var formData = new FormData(this);
        
        $.ajax({
            type: 'POST',
            url: "{{ route('category-finance.store') }}",
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            beforeSend: function() {
                $('#btnSaveKategoriKeuangan').html('Loading...');
                $('#btnSaveKategoriKeuangan').attr('disabled', true);
            },
            success: (data) => {
                $('#categoryFinanceModal').modal('hide');
                $('#tb_category_finance').DataTable().ajax.reload();
            },
            complete: () => {
                $('#categoryFinanceModal').modal('hide');
            },
            error: function(data){
                
            }
        });
    })
    $('#tb_category_finance').dataTable({
            processing: true,
            serverSide: true,
            ajax: {
                type: 'get',
                url: "{{ route('category-finance.index') }}",
            },
            columns: [
                { data: 'DT_RowIndex', name: 'id'},
                { data: 'name_category_finances', name: 'name_category_finances'},
                { data: 'created_at', name: 'created_at'},
                { data: 'updated_at', name: 'updated_at'},
                {
                    data: 'action',
                    searchable: false,
                    sortable: false
                }
            ]
        });

</script>