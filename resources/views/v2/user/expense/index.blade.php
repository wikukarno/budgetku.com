@extends('layouts.v2.app')

@section('title', 'Expense')

@section('content')
<div class="card bg-white border-0 rounded-3 mb-4">
    <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
            <h3 class="mb-0">
                Expense
            </h3>

            <a href="{{ route('customer.expense.create') }}" class="btn btn-primary" onclick="addCategoryIncome();" type="button">
                <i data-feather="plus" class="me-2"></i>
                Add New
            </a>
        </div>

        <div class="default-table-area all-products">
            <div class="table-responsive">
                <table class="table align-middle" id="expenseTable">
                    <thead>
                        <tr>
                            <th scope="col">Number</th>
                            <th scope="col">Category</th>
                            <th scope="col">
                                Name
                            </th>
                            <th scope="col">
                                Price
                            </th>
                            <th scope="col">
                                Date
                            </th>
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

@endsection

@push('after-scripts')
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

<script>
        function deleteExpense(id){
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
                        url: "{{ route('customer.expense.destroy') }}",
                        data: {
                            "_token": "{{ csrf_token() }}",
                            id:id
                        },
                        dataType: 'json',
                        beforeSend: function() {
                            $(".preloader").fadeIn();
                        },
                        success: function(res){
                            $('#expenseTable').DataTable().ajax.reload();
                            showCustomAlert('success', res.message);
                        },
                        complete: function(){
                            $(".preloader").fadeOut();
                        }
                    });
                }
            })
        }
        
        $('#expenseTable').dataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{!! url()->current() !!}",
            },
            columns: [
                { data: 'DT_RowIndex', name: 'id'},
                { data: 'category_finances_id', name: 'category_finances_id'},
                { data: 'name_item', name: 'name_item'},
                { data: 'price', name: 'price'},
                { data: 'purchase_date', name: 'purchase_date'},
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
                
                wrapper.insertBefore($('#expenseTable'));

            }
        });
</script>
@endpush