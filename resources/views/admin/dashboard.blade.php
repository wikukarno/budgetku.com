@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="row mb-3">
    <div class="col-12">
        <h6 class="font-weight-bolder mb-0">Dashboard</h6>
        <p class="text-sm mb-0">
            Ini adalah halaman dashboard, Anda dapat melihat laporan keuangan Anda disini.
        </p>
        
        @if ($sisaGaji <= $monthlyReport)
            <span class="text-danger text-sm font-weight-bolder">
                {{ $keterangan }}, Anda telah melebihi batas pengeluaran bulan ini.
            </span>
        @else
            <span class="text-success text-sm font-weight-bolder">
                {{ $keterangan }}
            </span>
        @endif
    </div>
</div>
<div class="row mb-3">
    <div class="col-xl-6 col-sm-6 mb-3">
        <div class="card">
            <div class="card-body p-3">
                <div class="row">
                    <div class="col-8">
                        <div class="numbers">
                            <p class="text-sm mb-0 text-capitalize font-weight-bold">Sisa Gaji Bulan
                                {{ \Carbon\Carbon::parse($tanggalGajiBulanKemarin)->isoFormat('MMMM') }}
                            </p>
                            <h5 class="font-weight-bolder mb-0">
                                Rp.{{ number_format($sisaGaji, 0, ',', '.') }}
                            </h5>
                        </div>
                    </div>
                    <div class="col-4 text-end">
                        <div class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md">
                            <i class="ni ni-cart text-lg opacity-10" aria-hidden="true"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-6 col-sm-6 mb-xl-0">
        <div class="card">
            <div class="card-body p-3">
                <div class="row">
                    <div class="col-8">
                        <div class="numbers">
                            <p class="text-sm mb-0 text-capitalize font-weight-bold position-relative">Pengeluaran Bulan
                                {{ \Carbon\Carbon::now()->isoFormat('MMMM') }}
                                @if ($sisaGaji <= $monthlyReport) <span
                                    class="position-absolute top-0 start-0 bullet translate-middle p-2 bg-danger border border-light rounded-circle">
                                    <span class="visually-hidden">New alerts</span>
                                    </span>
                                    @else
                                    <span
                                        class="position-absolute top-0 start-0 bullet translate-middle p-2 bg-success border border-light rounded-circle">
                                        <span class="visually-hidden">New alerts</span>
                                    </span>
                                    @endif
                            </p>
                            <h5 class="font-weight-bolder mb-0">
                                Rp.{{ number_format($monthlyReport, 0, ',', '.') }}

                            </h5>
                        </div>
                    </div>
                    <div class="col-4 text-end">
                        <div class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md">
                            <i class="ni ni-money-coins text-lg opacity-10" aria-hidden="true"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row mb-3">
    <div class="col-xl-6 col-sm-6 mb-3">
        <div class="card">
            <div class="card-body p-3">
                <div class="row">
                    <div class="col-8">
                        <div class="numbers">
                            <p class="text-sm mb-0 text-capitalize font-weight-bold">Tagihan Perbulan {{
                                \Carbon\Carbon::now()->isoFormat('MMMM') ?? ''
                                }}</p>
                            <h5 class="font-weight-bolder mb-0">
                                Rp.{{ number_format($monthlyBills, 0, ',', '.') }}
                            </h5>
                        </div>
                    </div>
                    <div class="col-4 text-end">
                        <div class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md">
                            <i class="ni ni-cart text-lg opacity-10" aria-hidden="true"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-6 col-sm-6 mb-xl-0">
        <div class="card">
            <div class="card-body p-3">
                <div class="row">
                    <div class="col-8">
                        <div class="numbers">
                            <p class="text-sm mb-0 text-capitalize font-weight-bold position-relative">Tagihan Pertahun
                                {{ \Carbon\Carbon::now()->isoFormat('YYYY') }}
                            </p>
                            <h5 class="font-weight-bolder mb-0">
                                Rp.{{ number_format($yearlyBills, 0, ',', '.') }}

                                {{-- <span class="text-success text-sm font-weight-bolder">+55%</span> --}}
                            </h5>
                        </div>
                    </div>
                    <div class="col-4 text-end">
                        <div class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md">
                            <i class="ni ni-money-coins text-lg opacity-10" aria-hidden="true"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row mb-3">
    <div class="col-xl-6 col-sm-6 mb-xl-0 mb-3">
        <div class="card">
            <div class="card-body p-3">
                <div class="row">
                    <div class="col-8">
                        <div class="numbers">
                            <p class="text-sm mb-0 text-capitalize font-weight-bold">Pengeluaran Minggu Ini
                                {{
                                \Carbon\Carbon::now()->addDays(-6)->isoFormat('DD MMMM YYYY')
                                . ' - ' .
                                \Carbon\Carbon::now()->isoFormat('dddd') == 'Minggu' ?
                                \Carbon\Carbon::now()->isoFormat('DD') :
                                \Carbon\Carbon::now()->startOfWeek()->isoFormat('DD') . ' - ' .
                                \Carbon\Carbon::now()->endOfWeek()->isoFormat('DD MMMM YYYY')
                                }}
                            </p>
                            <h5 class="font-weight-bolder mb-0">
                                Rp.{{ number_format($weeklyReport, 0, ',', '.') }}
                                {{-- <span class="text-success text-sm font-weight-bolder">+55%</span> --}}
                            </h5>
                        </div>
                    </div>
                    <div class="col-4 text-end">
                        <div class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md">
                            <i class="ni ni-money-coins text-lg opacity-10" aria-hidden="true"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-6 col-sm-6 mb-xl-0">
        <div class="card">
            <div class="card-body p-3">
                <div class="row">
                    <div class="col-8">
                        <div class="numbers">
                            <p class="text-sm mb-0 text-capitalize font-weight-bold">Pengeluaran Hari
                                {{ \Carbon\Carbon::now()->isoFormat('dddd') }}
                            </p>
                            <h5 class="font-weight-bolder mb-0">
                                Rp.{{ number_format($todayExpenditure, 0, ',', '.') }}
                                {{-- <span class="text-success text-sm font-weight-bolder">+55%</span> --}}
                            </h5>
                        </div>
                    </div>
                    <div class="col-4 text-end">
                        <div class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md">
                            <i class="ni ni-money-coins text-lg opacity-10" aria-hidden="true"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row mb-3">
    <div class="col-xl-12 col-sm-12 mb-xl-0 mb-3">
        <div class="card">
            <div class="card-body p-3">
                <div class="row">
                    <div class="col-8">
                        <div class="numbers">
                            <p class="text-sm mb-0 text-capitalize font-weight-bold">Pengeluaran Tahun
                                {{ \Carbon\Carbon::now()->isoFormat('YYYY') }}
                            </p>
                            <h5 class="font-weight-bolder mb-0">
                                Rp.{{ number_format($anualReport, 0, ',', '.') }}
                                {{-- <span class="text-success text-sm font-weight-bolder">+55%</span> --}}
                            </h5>
                        </div>
                    </div>
                    <div class="col-4 text-end">
                        <div class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md">
                            <i class="ni ni-money-coins text-lg opacity-10" aria-hidden="true"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row mb-3">
    <div class="col-xl-6 col-sm-6 mb-xl-0 mb-4">
        <div class="card">
            <div class="card-body p-3">
                <div class="row">
                    <div class="col-8">
                        <div class="numbers">
                            <p class="text-sm mb-0 text-capitalize font-weight-bold">Kategori Keuangan</p>
                            <h5 class="font-weight-bolder mb-0">
                                {{ $categoryFinances }}
                                {{-- <span class="text-danger text-sm font-weight-bolder">-2%</span> --}}
                            </h5>
                        </div>
                    </div>
                    <div class="col-4 text-end">
                        <div class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md">
                            <i class="ni ni-paper-diploma text-lg opacity-10" aria-hidden="true"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-6 col-sm-6 mb-xl-0 mb-4">
        <div class="card">
            <div class="card-body p-3">
                <div class="row">
                    <div class="col-8">
                        <div class="numbers">
                            <p class="text-sm mb-0 text-capitalize font-weight-bold">Portofolio</p>
                            <h5 class="font-weight-bolder mb-0">
                                {{ $portofolios }}
                                {{-- <span class="text-danger text-sm font-weight-bolder">-2%</span> --}}
                            </h5>
                        </div>
                    </div>
                    <div class="col-4 text-end">
                        <div class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md">
                            <i class="ni ni-image text-lg opacity-10" aria-hidden="true"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection