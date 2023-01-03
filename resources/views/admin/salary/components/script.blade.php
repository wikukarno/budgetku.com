<script>
    function addSalary() {
        $('#salaryModalLabel').html('Tambah Uang Masuk');
        $('#salaryModal').modal('show');
        $('#id_salary').val('');
        $('#form-salary').trigger('reset');
        $('#btnSaveSalary').html('Simpan');
        $('#btnSaveSalary').attr('disabled', false);
    }

    function updateSalary(id){
        $('#salaryModalLabel').html('Edit Uang Masuk');
        $('#salaryModal').modal('show');
        $('#form-salary').trigger('reset');
        $('#id_salary').val(id);
        $('#btnSaveSalary').html('Simpan Perubahan');
        $('#btnSaveSalary').attr('disabled', false);

        $.ajax({
            type:"POST",
            url: "{{ url('/pages/dashboard/show/salary') }}",
            data: {
                "_token": "{{ csrf_token() }}",
                id:id
            },
            dataType: 'json',
            beforeSend: function() {
                $(".preloader").fadeIn();
            },
            success: function(res){
                $('#id_salary').val(res.id);
                $('#salary').val(formatRupiah(res.salary, 'Rp.'));
                $('#date').val(res.date);
                $('#description').val(res.description);
            },
            complete: function(){
                $(".preloader").fadeOut();
            }
        });
        
    }

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
                    type:"POST",
                    url: "{{ url('/pages/dashboard/delete/salary') }}",
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

    $('#tb_salary').dataTable({
        processing: true,
        serverSide: true,
        ajax: {
            type: 'GET',
            url: "{{ route('salary.index') }}",
        },
        columns: [
            { data: 'DT_RowIndex', name: 'id'},
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

    $('#form-salary').submit(function(e){
        e.preventDefault();
        var formData = new FormData(this);

        $.ajax({
            type: 'POST',
            url: "{{ route('salary.store') }}",
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            beforeSend: function() {
                $('#btnSaveSalary').html('Loading...');
                $('#btnSaveSalary').attr('disabled', true);
            },
            success: (data) => {
                $('#salaryModal').modal('hide');
                $('#tb_salary').DataTable().ajax.reload();
            },
            complete: () => {
                $('#salaryModal').modal('hide');
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

    var rupiah = document.getElementById('salary');
    rupiah.addEventListener('keyup', function(e){
        rupiah.value = formatRupiah(this.value, 'Rp. ');
    });

</script>