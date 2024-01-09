<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

<script>

    function deleteBill(id){
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
                    type:"POST",
                    url: "{{ url('/pages/dashboard/delete/bill') }}",
                    data: {
                        "_token": "{{ csrf_token() }}",
                        id:id
                    },
                    dataType: 'json',
                    beforeSend: function() {
                        $(".preloader").fadeIn();
                    },
                    success: function(res){
                        $('#tb_bill').DataTable().ajax.reload();
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

    $('#tb_bill').dataTable({
        processing: true,
        serverSide: true,
        ajax: {
            type: 'GET',
            url: "{{ route('bill.index') }}",
        },
        columns: [
            { data: 'DT_RowIndex', name: 'id'},
            { data: 'pemilik_tagihan', name: 'pemilik_tagihan' },
            { data: 'nama_tagihan', name: 'nama_tagihan' },
            { data: 'harga_tagihan', name: 'harga_tagihan' },
            { data: 'siklus_tagihan', name: 'siklus_tagihan' },
            { data: 'jatuh_tempo_tagihan', name: 'jatuh_tempo_tagihan' },
            { data: 'metode_pembayaran', name: 'metode_pembayaran' },
            {
                data: 'action',
                searchable: false,
                sortable: false
            }
        ]
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

    var hargaTagihan = document.getElementById('harga_tagihan');
    hargaTagihan.addEventListener('keyup', function(e){
        hargaTagihan.value = formatRupiah(this.value, 'Rp. ');
    });

</script>