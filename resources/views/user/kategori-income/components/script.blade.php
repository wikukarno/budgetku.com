<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

<script>
    function addCategoryIncome() {
        $('#categoryIncomeModal').modal('show');
        $('#categoryIncomeModalLabel').html('Tambah Kategori Uang Masuk');
        $('#id_category_income').val('');
        $('#form-tambah-kategori-income').trigger('reset');
        $('#btnSaveKategoriKeuangan').html('Simpan');
        $('#btnSaveKategoriKeuangan').attr('disabled', false);
    }

    function updateKategoriIncome(id){
        $('#form-tambah-kategori-income').trigger('reset');
        $('#categoryIncomeModal').modal('show');
        $('#categoryIncomeModalLabel').html('Update Kategori Uang Masuk');
        $('#id_category_income').val(id);
        $('#btnSaveKategoriKeuangan').html('Simpan Perubahan');
        $('#btnSaveKategoriKeuangan').attr('disabled', false);
        
        $.ajax({
            type:"GET",
            url: "{{ route('category-income.show') }}",
            data: {
                "_token": "{{ csrf_token() }}",
                id:id
            },
            dataType: 'json',
            beforeSend: function() {
                $(".preloader").fadeIn();
            },
            success: function(res){
                $('#id_kategori_income').val(res.id);
                $('#name_category_incomes').val(res.name_category_incomes);
            },
            complete: function(){
                $(".preloader").fadeOut();
            }
        });
    }

    function deleteKategoriIncome(id){
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
                    type:"DELETE",
                    url: "{{ route('category-income.destroy') }}",
                    data: {
                        "_token": "{{ csrf_token() }}",
                        id:id
                    },
                    dataType: 'json',
                    beforeSend: function() {
                        $(".preloader").fadeIn();
                    },
                    success: function(res){
                        $('#tb_kategori_uang_masuk').DataTable().ajax.reload();
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
    
    $('#form-tambah-kategori-income').submit(function (e){
        e.preventDefault();
        var formData = new FormData(this);
        
        $.ajax({
            type: 'POST',
            url: "{{ route('category-income.store') }}",
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            beforeSend: function() {
                $('#btnSaveKategoriKeuangan').html('Loading...');
                $('#btnSaveKategoriKeuangan').attr('disabled', true);
            },
            success: (data) => {
                $('#categoryIncomeModal').modal('hide');
                $('#tb_kategori_uang_masuk').DataTable().ajax.reload();
            },
            complete: () => {
                $('#categoryIncomeModal').modal('hide');
            },
            error: function(data){
                
            }
        });
    })
    $('#tb_kategori_uang_masuk').dataTable({
            processing: true,
            serverSide: true,
            ajax: {
                type: 'get',
                url: "{!! url()->current() !!}",
            },
            columns: [
                { data: 'DT_RowIndex', name: 'id'},
                { data: 'name_category_incomes', name: 'name_category_incomes'},
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