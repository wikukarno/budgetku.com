<script>
    function addFinance() {
        $('#financeModalLabel').html('Tambah Data Keuangan');
        $('#financeModal').modal('show');
        $('#id_finance').val('');
        $('#form-finance').trigger('reset');
        $('#btnSaveKeuangan').html('Simpan');
        $('#btnSaveKeuangan').attr('disabled', false);
    }

    function updateFinance(id){
        $('#financeModalLabel').html('Edit Kategori Keuangan');
        $('#financeModal').modal('show');
        $('#form-finance').trigger('reset');
        $('#id_finance').val(id);
        $('#btnSaveKeuangan').html('Simpan Perubahan');
        $('#btnSaveKeuangan').attr('disabled', false);

        $.ajax({
            type:"POST",
            url: "{{ url('/pages/dashboard/show/finance') }}",
            data: {
                "_token": "{{ csrf_token() }}",
                id:id
            },
            dataType: 'json',
            beforeSend: function() {
                $(".preloader").fadeIn();
            },
            success: function(res){
                $('#id_finance').val(res.id);
                $('#category_finances_id').val(res.category_finances_id);
                $('#name_item').val(res.name_item);
                $('#price').val(formatRupiah(res.price, 'Rp.'));
                $('#purchase_date').val(res.purchase_date);
                $('#purchase_by').val(res.purchase_by);
            },
            complete: function(){
                $(".preloader").fadeOut();
            }
        });
        
    }

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
                    type:"POST",
                    url: "{{ url('/pages/dashboard/delete/finance') }}",
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
            { data: 'purchase_by', name: 'purchase_by' },
            { data: 'purchase_date', name: 'purchase_date' },
            {
                data: 'action',
                searchable: false,
                sortable: false
            }
        ]
    });
    
    $('#form-finance').submit(function(e){
        e.preventDefault();
        var formData = new FormData(this);

        $.ajax({
            type: 'POST',
            url: "{{ route('finance.store') }}",
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            beforeSend: function() {
                $('#btnSaveKeuangan').html('Loading...');
                $('#btnSaveKeuangan').attr('disabled', true);
            },
            success: (data) => {
                $('#financeModal').modal('hide');
                $('#tb_finance').DataTable().ajax.reload();
            },
            complete: () => {
                $('#financeModal').modal('hide');
            },
            error: function(data){
                console.log(data);
            }
        });
    })

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