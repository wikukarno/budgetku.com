@extends('layouts.v2.app')

@section('title', 'Expense Category')
    
@section('content')
    <div class="card bg-white border-0 rounded-3 mb-4">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
                <h3 class="mb-0">
                    Expense Category
                </h3>

                <button class="btn btn-primary" onclick="addCategoryExpense();" type="button">
                    <i data-feather="plus" class="me-2"></i>
                    Add New
                </button>
            </div>
    
            <div class="default-table-area all-products">
                <div class="table-responsive">
                    <table class="table align-middle" id="categoryExpenseTable">
                        <thead>
                            <tr>
                                <th scope="col">Number</th>
                                <th scope="col">Name</th>
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

    <div class="modal fade" id="categoryExpenseModal" tabindex="-1" aria-labelledby="categoryExpenseModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="categoryExpenseModalLabel"></h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="form-tambah-kategori-finance" method="POST">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="id" id="id_category_finance">
                        <div class="form-group">
                            <label for="name">
                                Name
                            </label>
                            <input type="text" name="name_category_finances" id="name_category_finances" class="form-control"
                                placeholder="Name" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger text-white" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" id="btnSaveKategoriKeuangan" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('after-scripts')
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        function addCategoryExpense() {
            $('#categoryExpenseModal').modal('show');
            $('#categoryExpenseModalLabel').html('Add New Expense Category');
            $('#id_category_finance').val('');
            $('#form-tambah-kategori-finance').trigger('reset');
            $('#btnSaveKategoriKeuangan').html('Save');
            $('#btnSaveKategoriKeuangan').attr('disabled', false);
        }

        function updateKategoriFinance(id){
            $('#form-tambah-kategori-finance').trigger('reset');
            $('#categoryExpenseModal').modal('show');
            $('#categoryExpenseModalLabel').html('Edit Expense Category');
            $('#id_category_finance').val(id);
            $('#btnSaveKategoriKeuangan').html('Save');
            $('#btnSaveKategoriKeuangan').attr('disabled', false);
            
            $.ajax({
                type:"GET",
                url: "{{ route('admin.category.expense.show') }}",
                data: {
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
                title: 'Are you sure?',
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
                        url: "{{ route('admin.category.expense.destroy') }}",
                        data: {
                            "_token": "{{ csrf_token() }}",
                            id:id
                        },
                        dataType: 'json',
                        beforeSend: function() {
                            $(".preloader").fadeIn();
                        },
                        success: function(res){
                            $('#categoryExpenseTable').DataTable().ajax.reload();
                            showCustomAlert('success', res.message);
                        },
                        complete: function(){
                            $(".preloader").fadeOut();
                        },
                        error: function(data){
                            showCustomAlert('danger', data.responseJSON.message);
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
                url: "{{ route('admin.category.expense.store') }}",
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
                    $('#form-tambah-kategori-finance').trigger('reset');
                    $('#categoryExpenseModal').modal('hide');
                    $('#categoryExpenseTable').DataTable().ajax.reload();
                },
                complete: () => {
                    $('#categoryExpenseModal').modal('hide');
                },
                error: function(data){
                    showCustomAlert('danger', data.responseJSON.message);
                    $('#btnSaveKategoriKeuangan').html('Save');
                    $('#btnSaveKategoriKeuangan').attr('disabled', false);
                }
            });
        });

        $('#categoryExpenseTable').dataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{!! url()->current() !!}",
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
                
                wrapper.insertBefore($('#categoryExpenseTable'));

            }
        });
    </script>
@endpush        