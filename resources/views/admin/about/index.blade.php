@extends('layouts.app')

@section('title', 'Tentang')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-lg-flex d-md-flex align-items-center justify-content-between">
                <h4>Tentang Saya</h4>
                <a href="{{ route('about.create') }}" class="btn btn-primary mt-3"><i
                        class="fa-solid fa-circle-plus"></i> &nbsp; Tambah Data</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="tb_about" class="table table-hover scroll-horizontal-vertical w-100">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Keterangan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('after-scripts')
<script>
    $('#tb_about').dataTable({
            processing: true,
            serverSide: true,
            ajax: {
                type: 'get',
                url: "{{ route('about.index') }}",
            },
            columns: [
                { data: 'DT_RowIndex', name: 'id'},
                {
                    data: 'description', 
                    name: 'description',
                    render: function(data) {
                        if(data != null) return data.slice(0,100) + '...' ;
                        else return 'No Data', data;
                    },
                    width: '60%'

                },
                {
                    data: 'action',
                    searchable: false,
                    sortable: false
                }
            ]
        });

</script>
@endpush

@push('after-styles')
<style>
    table.dataTable tbody tr td {
        word-wrap: break-word;
        word-break: break-all;
    }
</style>
@endpush