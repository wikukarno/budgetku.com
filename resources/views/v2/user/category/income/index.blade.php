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
                        <input type="hidden" name="uuid" id="id_category_income">
                        <div class="form-group">
                            <label for="name">
                                Name
                            </label>
                            <input type="text" name="name_category_incomes" id="name_category_incomes" class="form-control"
                                placeholder="Salary or other" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger text-white" data-bs-dismiss="modal">
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
        console.log('Category Income JavaScript loaded!');
        
        // Make functions globally accessible for debugging
        window.updateKategoriIncomeDebug = function(uuid) {
            console.log('Manual test function called with UUID:', uuid);
            updateKategoriIncome(uuid);
        };
        function addCategoryIncome() {
            $('#categoryIncomeModal').modal('show');
            $('#categoryIncomeModalLabel').html('Add New Income Category');
            $('#id_category_income').val('');
            $('#form-tambah-kategori-income').trigger('reset');
            $('#btnSaveKategoriKeuangan').html('Save');
            $('#btnSaveKategoriKeuangan').attr('disabled', false);
            
            // Clear any previous validation errors
            $('.form-control').removeClass('is-invalid');
            $('.invalid-feedback').remove();
            
            // Focus on first input for better UX
            setTimeout(() => {
                $('#name_category_incomes').focus();
            }, 500);
        }

        function updateKategoriIncome(uuid){
            console.log('updateKategoriIncome called with UUID:', uuid);
            
            $('#form-tambah-kategori-income').trigger('reset');
            $('#categoryIncomeModal').modal('show');
            $('#categoryIncomeModalLabel').html('Edit Income Category');
            $('#id_category_income').val(uuid);
            $('#btnSaveKategoriKeuangan').html('Update');
            $('#btnSaveKategoriKeuangan').attr('disabled', false);
            
            console.log('Making AJAX request to:', "{{ route('customer.category.income.show') }}");
            
            $.ajax({
                type:"GET",
                url: "{{ route('customer.category.income.show') }}",
                data: {
                    "_token": "{{ csrf_token() }}",
                    uuid:uuid
                },
                dataType: 'json',
                beforeSend: function() {
                    console.log('AJAX request started');
                    $(".preloader").fadeIn();
                },
                success: function(res){
                    console.log('AJAX response received:', res);
                    
                    // Check if response has data structure
                    if (res.status === 'success' && res.data) {
                        $('#id_category_income').val(res.data.uuid);
                        $('#name_category_incomes').val(res.data.name_category_incomes);
                        console.log('Form populated with:', res.data);
                    } else {
                        // Handle old response format
                        $('#id_category_income').val(res.uuid);
                        $('#name_category_incomes').val(res.name_category_incomes);
                        console.log('Form populated with old format:', res);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', xhr.responseText);
                    showCustomAlert('danger', 'Failed to load category data');
                },
                complete: function(){
                    console.log('AJAX request completed');
                    $(".preloader").fadeOut();
                }
            });
        }

        function deleteKategoriIncome(uuid){
            Swal.fire({
                title: 'Delete Category?',
                text: "This action cannot be undone. Are you sure you want to delete this income category?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fas fa-trash"></i> Yes, Delete',
                cancelButtonText: '<i class="fas fa-times"></i> Cancel',
                buttonsStyling: false,
                customClass: {
                    confirmButton: 'btn btn-danger me-2',
                    cancelButton: 'btn btn-secondary'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading state
                    Swal.fire({
                        title: 'Deleting...',
                        text: 'Please wait while we delete the category.',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    
                    $.ajax({
                        type:"DELETE",
                        url: "{{ route('customer.category.income.destroy') }}",
                        data: {
                            "_token": "{{ csrf_token() }}",
                            uuid:uuid
                        },
                        dataType: 'json',
                        success: function(res){
                            $('#categoryIncomeTable').DataTable().ajax.reload();
                            Swal.fire({
                                title: 'Deleted!',
                                text: res.message || 'Category has been deleted successfully.',
                                icon: 'success',
                                timer: 2000,
                                showConfirmButton: false
                            });
                        },
                        error: function(xhr, status, error) {
                            console.error('Delete error:', xhr.responseJSON);
                            let message = 'Failed to delete category. Please try again.';
                            
                            if (xhr.status === 403) {
                                message = 'You do not have permission to delete this category.';
                            } else if (xhr.status === 404) {
                                message = 'Category not found or already deleted.';
                            } else if (xhr.responseJSON?.message) {
                                message = xhr.responseJSON.message;
                            }
                            
                            Swal.fire({
                                title: 'Error!',
                                text: message,
                                icon: 'error',
                                confirmButtonColor: '#dc3545'
                            });
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
                url: "{{ route('customer.category.income.store') }}",
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                beforeSend: function() {
                    $('#btnSaveKategoriKeuangan').html('<span class="spinner-border spinner-border-sm me-2" role="status"></span>Saving...');
                    $('#btnSaveKategoriKeuangan').attr('disabled', true);
                    $('.form-control').attr('readonly', true);
                },
                success: (data) => {
                    console.log('Form submission success:', data);
                    showCustomAlert('success', data.message || 'Category saved successfully!');
                    $('#form-tambah-kategori-income').trigger('reset');
                    $('#categoryIncomeModal').modal('hide');
                    $('#categoryIncomeTable').DataTable().ajax.reload();
                    
                    // Clear any existing error states
                    $('.form-control').removeClass('is-invalid');
                    $('.invalid-feedback').remove();
                },
                complete: () => {
                    $('#btnSaveKategoriKeuangan').html('Save');
                    $('#btnSaveKategoriKeuangan').attr('disabled', false);
                    $('.form-control').attr('readonly', false);
                },
                error: function(xhr, status, error){
                    console.error('Form submission error:', xhr.responseJSON);
                    
                    // Clear previous error states
                    $('.form-control').removeClass('is-invalid');
                    $('.invalid-feedback').remove();
                    
                    if (xhr.status === 422) {
                        // Validation errors
                        let errors = xhr.responseJSON.errors || {};
                        let message = xhr.responseJSON.message || 'Please check your input and try again.';
                        
                        showCustomAlert('danger', message);
                        
                        // Display field-specific errors
                        Object.keys(errors).forEach(field => {
                            let fieldElement = $(`[name="${field}"]`);
                            if (fieldElement.length) {
                                fieldElement.addClass('is-invalid');
                                fieldElement.after(`<div class="invalid-feedback">${errors[field][0]}</div>`);
                            }
                        });
                        
                    } else if (xhr.status === 403) {
                        showCustomAlert('danger', 'You do not have permission to perform this action.');
                    } else if (xhr.status === 500) {
                        showCustomAlert('danger', 'Server error occurred. Please try again later.');
                    } else {
                        showCustomAlert('danger', xhr.responseJSON?.message || 'An unexpected error occurred. Please try again.');
                    }
                    
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
                { data: 'DT_RowIndex', name: 'uuid'},
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
                console.log('DataTables initialized!');
                console.log('Checking action buttons...');
                
                const lengthEl = $('.dataTables_length');
                const filterEl = $('.dataTables_filter');
                
                const wrapper = $('<div class="dt-top w-100"></div>');
                lengthEl.appendTo(wrapper);
                filterEl.appendTo(wrapper);
                
                wrapper.insertBefore($('#categoryIncomeTable'));

                // Check if action buttons are rendered
                setTimeout(function() {
                    const actionButtons = $('[onclick*="updateKategoriIncome"]');
                    console.log('Found action buttons:', actionButtons.length);
                    if (actionButtons.length > 0) {
                        console.log('Action button onclick:', actionButtons.first().attr('onclick'));
                    }
                }, 1000);
            }
        });
    </script>
@endpush        