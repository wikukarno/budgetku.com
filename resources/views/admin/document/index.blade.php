@extends('layouts.app')

@section('title', 'Dokumen')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-lg-flex d-md-flex align-items-center justify-content-between">
                <h4>Dokumen</h4>
                <a href="{{ route('document.create') }}" class="btn btn-primary mt-3"><i
                        class="fa-solid fa-circle-plus"></i> &nbsp; Tambah Data</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="tb_document" class="table table-hover scroll-horizontal-vertical w-100">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nama</th>
                                <th>file</th>
                                <th>Action</th>
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
    $('#tb_document').dataTable({
            processing: true,
            serverSide: true,
            ajax: {
                type: 'get',
                url: "{{ route('document.index') }}",
            },
            columns: [
                { data: 'DT_RowIndex', name: 'id'},
                {
                    data: 'name', name: 'name',
                },
                {
                    data: 'file', name: 'file',
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