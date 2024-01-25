@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

<div class="row">
    <div class="col-xl-4 col-sm-6 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-9">
                        <div class="d-flex align-items-center align-self-start">
                            <h3 class="mb-0">
                                Rp.{{ number_format($totalPendapatan, 0, ',', '.') ?? 0 }}
                            </h3>
                            {{-- <p class="text-success ms-2 mb-0 font-weight-medium">+3.5%</p> --}}
                        </div>
                    </div>
                    {{-- <div class="col-3">
                        <div class="icon icon-box-success ">
                            <span class="mdi mdi-arrow-top-right icon-item"></span>
                        </div>
                    </div> --}}
                </div>
                <h6 class="text-muted font-weight-normal">Saldo</h6>
            </div>
        </div>
    </div>
    <div class="col-xl-8 col-sm-6 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-9">
                        <div class="d-flex align-items-center align-self-start">
                            <h3 class="mb-0">
                                Rp.{{ number_format($pengeluaran, 0, ',', '.') }}
                            </h3>
                            {{-- <p class="text-success ms-2 mb-0 font-weight-medium">+11%</p> --}}
                        </div>
                    </div>
                    {{-- <div class="col-3">
                        <div class="icon icon-box-success">
                            <span class="mdi mdi-arrow-top-right icon-item"></span>
                        </div>
                    </div> --}}
                </div>
                <h6 class="text-muted font-weight-normal">
                    Pengeluaran Bulan {{ \Carbon\Carbon::now()->addDays(-30)->isoFormat('MMMM') }}
                    &
                    {{ \Carbon\Carbon::now()->isoFormat('MMMM') }}
                </h6>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-xl-6 col-sm-6 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-9">
                        <div class="d-flex align-items-center align-self-start">
                            <h3 class="mb-0">
                                Rp.{{ number_format($monthlyBills, 0, ',', '.') }}
                            </h3>
                            {{-- <p class="text-danger ms-2 mb-0 font-weight-medium">-2.4%</p> --}}
                        </div>
                    </div>
                    {{-- <div class="col-3">
                        <div class="icon icon-box-danger">
                            <span class="mdi mdi-arrow-bottom-left icon-item"></span>
                        </div>
                    </div> --}}
                </div>
                <h6 class="text-muted font-weight-normal">Tagihan Perbulan</h6>
            </div>
        </div>
    </div>
    <div class="col-xl-6 col-sm-6 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-9">
                        <div class="d-flex align-items-center align-self-start">
                            <h3 class="mb-0">
                                Rp.{{ number_format($yearlyBills, 0, ',', '.') }}
                            </h3>
                            {{-- <p class="text-success ms-2 mb-0 font-weight-medium">+3.5%</p> --}}
                        </div>
                    </div>
                    {{-- <div class="col-3">
                        <div class="icon icon-box-success ">
                            <span class="mdi mdi-arrow-top-right icon-item"></span>
                        </div>
                    </div> --}}
                </div>
                <h6 class="text-muted font-weight-normal">Tagihan Pertahun</h6>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-sm-4 grid-margin">
        <div class="card">
            <div class="card-body">
                <h5>Pengeluaran minggu ini</h5>
                <h6 class="text-muted">
                    {{
                    \Carbon\Carbon::now()->addDays(-6)->isoFormat('DD MMMM YYYY')
                    . ' - ' .
                    \Carbon\Carbon::now()->isoFormat('dddd') == 'Minggu' ?
                    \Carbon\Carbon::now()->isoFormat('DD') :
                    \Carbon\Carbon::now()->startOfWeek()->isoFormat('DD') . ' - ' .
                    \Carbon\Carbon::now()->endOfWeek()->isoFormat('DD MMMM YYYY')
                    }}
                </h6>
                <div class="row">
                    <div class="col-8 col-sm-12 col-xl-8 my-auto">
                        <div class="d-flex d-sm-block d-md-flex align-items-center">
                            <h2 class="mb-0">
                                Rp.{{ number_format($weeklyReport, 0, ',', '.') }}
                            </h2>
                            {{-- <p class="text-success ms-2 mb-0 font-weight-medium">+3.5%</p> --}}
                        </div>
                        {{-- <h6 class="text-muted font-weight-normal">11.38% Since last month</h6> --}}
                    </div>
                    {{-- <div class="col-4 col-sm-12 col-xl-4 text-center text-xl-right">
                        <i class="icon-lg mdi mdi-codepen text-primary ms-auto"></i>
                    </div> --}}
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-4 grid-margin">
        <div class="card">
            <div class="card-body">
                <h5>Pengeluaran hari ini</h5>
                <h6 class="text-muted">
                    {{ \Carbon\Carbon::now()->isoFormat('dddd, DD MMMM YYYY') }}
                </h6>
                <div class="row">
                    <div class="col-8 col-sm-12 col-xl-8 my-auto">
                        <div class="d-flex d-sm-block d-md-flex align-items-center">
                            <h2 class="mb-0">
                                Rp.{{ number_format($todayExpenditure, 0, ',', '.') }}
                            </h2>
                            {{-- <p class="text-success ms-2 mb-0 font-weight-medium">+8.3%</p> --}}
                        </div>
                        {{-- <h6 class="text-muted font-weight-normal"> 9.61% Since last month</h6> --}}
                    </div>
                    {{-- <div class="col-4 col-sm-12 col-xl-4 text-center text-xl-right">
                        <i class="icon-lg mdi mdi-wallet-travel text-danger ms-auto"></i>
                    </div> --}}
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-4 grid-margin">
        <div class="card">
            <div class="card-body">
                <h5>Pengeluaran Tahun ini</h5>
                <h6 class="text-muted">
                    {{ \Carbon\Carbon::now()->isoFormat('YYYY') }} <a href="#">Detail pengeluaran</a>
                </h6>
                <div class="row">
                    <div class="col-8 col-sm-12 col-xl-8 my-auto">
                        <div class="d-flex d-sm-block d-md-flex align-items-center">
                            <h2 class="mb-0">
                                Rp.{{ number_format($anualReport, 0, ',', '.') }}
                            </h2>
                            {{-- <p class="text-danger ms-2 mb-0 font-weight-medium">-2.1% </p> --}}
                        </div>
                        {{-- <h6 class="text-muted font-weight-normal">2.27% Since last month</h6> --}}
                    </div>
                    {{-- <div class="col-4 col-sm-12 col-xl-4 text-center text-xl-right">
                        <i class="icon-lg mdi mdi-monitor text-success ms-auto"></i>
                    </div> --}}
                </div>
            </div>
        </div>
    </div>
</div>


<div class="row ">
    <div class="col-12 grid-margin">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Data Tagihan</h4>
                <div class="table-responsive">
                    <table id="tb_tagihan" class="table table-hover scroll-horizontal-vertical w-100">
                        <thead>
                            <tr>
                                <th> No </th>
                                <th> Nama Tagihan </th>
                                <th> Harga Tagihan </th>
                                <th> Jatuh Tempo </th>
                                <th> Siklus </th>
                                <th> Tipe Pembayaran </th>
                                <th> Aksi & Status </th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('after-scripts')
    <script>
        $('#tb_tagihan').dataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: {
                type: 'GET',
                url: "{!! url()->current() !!}",
            },
            columns: [
                { data: 'DT_RowIndex', name: 'id'},
                { data: 'nama_tagihan', name: 'nama_tagihan' },
                { data: 'harga_tagihan', name: 'harga_tagihan' },
                { data: 'jatuh_tempo_tagihan', name: 'jatuh_tempo_tagihan' },
                { data: 'siklus_tagihan', name: 'siklus_tagihan' },
                { data: 'metode_pembayaran', name: 'metode_pembayaran' },
                {
                    data: 'action',
                    searchable: false,
                    sortable: false
                }
            ]
        });
    </script>
@endpush