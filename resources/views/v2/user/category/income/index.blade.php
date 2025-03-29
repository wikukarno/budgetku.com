@extends('layouts.v2.app')

@section('title', 'Income Categories')
    
@section('content')
    <div class="card bg-white border-0 rounded-3 mb-4">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
                <h3 class="mb-0">
                    Income Categories
                </h3>
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
@endsection

@push('after-scripts')
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <script>
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