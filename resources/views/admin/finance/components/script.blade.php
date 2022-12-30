<script>
    function addFinance() {
        $('#financeModalLabel').html('Tambah Kategori Keuangan');
        $('#financeModal').modal('show');
        $('#id_finance').val('');
        $('#form-tambah-finance').trigger('reset');
    }
    $('#tb_finance').dataTable({
        processing: true,
        serverSide: true,
        ajax: {
            type: 'GET',
            url: "{{ route('finance.index') }}",
        },
        columns: [
            { data: 'DT_RowIndex', name: 'id'},
            { data: 'category_finance.name_category_finances', name: 'category_finance.name_category_finances' },
            { data: 'name_item', name: 'name_item' },
            { data: 'purchase_date', name: 'purchase_date' },
            { data: 'purchase_by', name: 'purchase_by' },
            { data: 'price', name: 'price' },
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
                $('#btnSaveFinance').html('Loading...');
                $('#btnSaveFinance').attr('disabled', true);
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