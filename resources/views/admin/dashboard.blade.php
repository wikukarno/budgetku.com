@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="row mb-3">
    <div class="col-12">
        <h6 class="font-weight-bolder mb-0">Dashboard</h6>
        </nav>
    </div>
</div>
<div class="row mb-3">
    <div class="col-xl-6 col-sm-6 mb-3">
        <div class="card">
            <div class="card-body p-3">
                <div class="row">
                    <div class="col-8">
                        <div class="numbers">
                            <p class="text-sm mb-0 text-capitalize font-weight-bold">Pendapatan Bulan {{
                                \Carbon\Carbon::now()->isoFormat('MMMM') }}</p>
                            <h5 class="font-weight-bolder mb-0">
                                Rp.{{ number_format($remainder, 0, ',', '.') }}
                                {{-- <span class="text-success text-sm font-weight-bolder">+5%</span> --}}
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
                            <p class="text-sm mb-0 text-capitalize font-weight-bold">Pengeluaran Bulan
                                {{ \Carbon\Carbon::now()->isoFormat('MMMM') }}
                            </p>
                            <h5 class="font-weight-bolder mb-0">
                                Rp.{{ number_format($expenditure, 0, ',', '.') }}
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
<div class="row mb-3">
    <div class="col-lg-7 mb-lg-0 mb-4">
        <div class="card">
            <div class="card-body p-3">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="d-flex flex-column h-100">
                            <p class="mb-1 pt-2 text-bold">Built by developers</p>
                            <h5 class="font-weight-bolder">Soft UI Dashboard</h5>
                            <p class="mb-5">From colors, cards, typography to complex elements, you will
                                find the full documentation.</p>
                            <a class="text-body text-sm font-weight-bold mb-0 icon-move-right mt-auto"
                                href="javascript:;">
                                Read More
                                <i class="fas fa-arrow-right text-sm ms-1" aria-hidden="true"></i>
                            </a>
                        </div>
                    </div>
                    <div class="col-lg-5 ms-auto text-center mt-5 mt-lg-0">
                        <div class="bg-gradient-primary border-radius-lg h-100">
                            <img src="../assets/img/shapes/waves-white.svg"
                                class="position-absolute h-100 w-50 top-0 d-lg-block d-none" alt="waves">
                            <div class="position-relative d-flex align-items-center justify-content-center h-100">
                                <img class="w-100 position-relative z-index-2 pt-4"
                                    src="../assets/img/illustrations/rocket-white.png" alt="rocket">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card h-100 p-3">
            <div class="overflow-hidden position-relative border-radius-lg bg-cover h-100"
                style="background-image: url('../assets/img/ivancik.jpg');">
                <span class="mask bg-gradient-dark"></span>
                <div class="card-body position-relative z-index-1 d-flex flex-column h-100 p-3">
                    <h5 class="text-white font-weight-bolder mb-4 pt-2">Work with the rockets</h5>
                    <p class="text-white">Wealth creation is an evolutionarily recent positive-sum game. It
                        is all about who take the opportunity first.</p>
                    <a class="text-white text-sm font-weight-bold mb-0 icon-move-right mt-auto" href="javascript:;">
                        Read More
                        <i class="fas fa-arrow-right text-sm ms-1" aria-hidden="true"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection