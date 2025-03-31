@extends('layouts.v2.app')

@section('title', 'Income Category')
    
@section('content')
    <div class="card bg-white border-0 rounded-3 mb-4">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
                <h3 class="mb-0">
                    Income Category
                </h3>

                <button class="btn btn-primary" onclick="addCategoryIncome();" type="button">
                    <i data-feather="plus" class="me-2"></i>
                    Add New
                </button>
            </div>
    
            <div class="default-table-area all-products">
                <div class="table-responsive">
                    <table class="table align-middle" id="categoryIncomeTable">
                        <thead>
                            <tr>
                                <th scope="col">Number</th>
                                <th scope="col">Income</th>
                                <th scope="col">Date</th>
                                <th scope="col">Description</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="categoryIncomeModal" tabindex="-1" aria-labelledby="categoryIncomeModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="categoryIncomeModalLabel"></h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="form-tambah-kategori-income" method="POST">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="id" id="id_category_income">
                        <div class="form-group">
                            <label for="name">
                                Name
                            </label>
                            <input type="text" name="name_category_incomes" id="name_category_incomes" class="form-control"
                                placeholder="Salary or other" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            Cancel
                        </button>
                        <button type="submit" id="btnSaveKategoriKeuangan" class="btn btn-primary">
                            Save
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('after-scripts')
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <script>
        function addCategoryIncome() {
            $('#categoryIncomeModal').modal('show');
            $('#categoryIncomeModalLabel').html('Add New Income Category');
            $('#id_category_income').val('');
            $('#form-tambah-kategori-income').trigger('reset');
            $('#btnSaveKategoriKeuangan').html('Save');
            $('#btnSaveKategoriKeuangan').attr('disabled', false);
        }

        function updateKategoriIncome(id){
            $('#form-tambah-kategori-income').trigger('reset');
            $('#categoryIncomeModal').modal('show');
            $('#categoryIncomeModalLabel').html('Edit Income Category');
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
                text: "Data will be deleted!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes',
                cancelButtonText: 'Cancel'
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
                            $('#categoryIncomeTable').DataTable().ajax.reload();
                            showCustomAlert('success', res.message);
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
                    showCustomAlert('success', data.message);
                    $('#form-tambah-kategori-income').trigger('reset');
                    $('#categoryIncomeModal').modal('hide');
                    $('#categoryIncomeTable').DataTable().ajax.reload();
                },
                complete: () => {
                    $('#categoryIncomeModal').modal('hide');
                },
                error: function(data){
                    showCustomAlert('danger', data.responseJSON.message);
                    $('#btnSaveKategoriKeuangan').html('Save');
                    $('#btnSaveKategoriKeuangan').attr('disabled', false);
                }
            });
        });

        $('#categoryIncomeTable').dataTable({
            processing: true,
            serverSide: true,
            ajax: {
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
            ],
            language: {
                search: "",
                searchPlaceholder: "Search...",
                zeroRecords: "No data available!",
                info: "Showing _START_ to _END_ of _TOTAL_ entries",
                lengthMenu: "Show _MENU_ entries",
                paginate: {
                    previous: "Previous",
                    next: "Next"
                }
            },
            initComplete: function () {
                const lengthEl = $('.dataTables_length');
                const filterEl = $('.dataTables_filter');
                
                const wrapper = $('<div class="dt-top w-100"></div>');
                lengthEl.appendTo(wrapper);
                filterEl.appendTo(wrapper);
                
                wrapper.insertBefore($('#categoryIncomeTable'));

            }
        });
    </script>
@endpush        