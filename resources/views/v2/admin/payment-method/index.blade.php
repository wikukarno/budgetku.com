@extends('layouts.v2.app')

@section('title', 'Payment Method')
    
@section('content')
    <div class="card bg-white border-0 rounded-3 mb-4">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
                <h3 class="mb-0">
                    Payment Method
                </h3>

                <button class="btn btn-primary" onclick="btnAddPaymentMethod();" type="button">
                    <i data-feather="plus" class="me-2"></i>
                    Add New
                </button>
            </div>
    
            <div class="default-table-area all-products">
                <div class="table-responsive">
                    <table class="table align-middle" id="paymentMethodTable">
                        <thead>
                            <tr>
                                <th scope="col">Number</th>
                                <th scope="col">Name</th>
                                <th scope="col">Created At</th>
                                <th scope="col">Updated At</th>
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

    <div class="modal fade" id="paymentMethodModal" tabindex="-1" aria-labelledby="paymentMethodModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="paymentMethodModalLabel"></h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="form-add-payment-method" method="POST">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="id" id="id_payment_method">
                        <div class="form-group">
                            <label for="name">
                                Name
                            </label>
                            <input type="text" name="name" id="name" class="form-control"
                                placeholder="Enter payment method name" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger text-white" data-bs-dismiss="modal">
                            Cancel
                        </button>
                        <button type="submit" id="btnSavePaymentMethod" class="btn btn-primary">
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
        function btnAddPaymentMethod() {
            $('#paymentMethodModal').modal('show');
            $('#paymentMethodModalLabel').html('Add New Payment Method');
            $('#id_payment_method').val('');
            $('#form-add-payment-method').trigger('reset');
            $('#btnSavePaymentMethod').html('Save');
            $('#btnSavePaymentMethod').attr('disabled', false);
        }

        function btnEditPaymentMethod(id){
            $('#form-add-payment-method').trigger('reset');
            $('#paymentMethodModal').modal('show');
            $('#paymentMethodModalLabel').html('Edit Payment Method');
            $('#id_payment_method').val(id);
            $('#btnSavePaymentMethod').html('Simpan Perubahan');
            $('#btnSavePaymentMethod').attr('disabled', false);
            
            $.ajax({
                type:"GET",
                url: "{{ route('admin.payment.method.show') }}",
                data: {
                    "_token": "{{ csrf_token() }}",
                    id:id
                },
                dataType: 'json',
                beforeSend: function() {
                    $(".preloader").fadeIn();
                },
                success: function(res){
                    $('#id_payment_method').val(res.data.id);
                    $('#name').val(res.data.name);
                },
                complete: function(){
                    $(".preloader").fadeOut();
                }
            });
        }

        function btnDeletePaymentMethod(id){
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
                        url: "{{ route('admin.payment.method.destroy') }}",
                        data: {
                            "_token": "{{ csrf_token() }}",
                            id:id
                        },
                        dataType: 'json',
                        beforeSend: function() {
                            $(".preloader").fadeIn();
                        },
                        success: function(res){
                            $('#paymentMethodTable').DataTable().ajax.reload();
                            showCustomAlert('success', res.message);
                        },
                        complete: function(){
                            $(".preloader").fadeOut();
                        }
                    });
                }
            })
        }
        
        $('#form-add-payment-method').submit(function (e){
            e.preventDefault();
            var formData = new FormData(this);
            
            $.ajax({
                type: 'POST',
                url: "{{ route('admin.payment.method.store') }}",
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                beforeSend: function() {
                    $('#btnSavePaymentMethod').html('Loading...');
                    $('#btnSavePaymentMethod').attr('disabled', true);
                },
                success: (data) => {
                    console.log(data);
                    showCustomAlert('success', data.message);
                    $('#form-add-payment-method').trigger('reset');
                    $('#paymentMethodModal').modal('hide');
                    $('#paymentMethodTable').DataTable().ajax.reload();
                },
                complete: () => {
                    $('#paymentMethodModal').modal('hide');
                },
                error: function(data){
                    showCustomAlert('danger', data.responseJSON.message);
                    $('#btnSavePaymentMethod').html('Save');
                    $('#btnSavePaymentMethod').attr('disabled', false);
                }
            });
        });

        $('#paymentMethodTable').dataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{!! url()->current() !!}",
            },
            columns: [
                { data: 'DT_RowIndex', name: 'id'},
                { data: 'name', name: 'name'},
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
                
                wrapper.insertBefore($('#paymentMethodTable'));

            }
        });
    </script>
@endpush        