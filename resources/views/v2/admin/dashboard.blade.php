@extends('layouts.v2.app')

@section('title', 'Dashboard')
    
@section('content')
    <div class="row justify-content-center">
        <div class="col-xl-12">
            <div class="card bg-white border-0 rounded-3 mb-4">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3 mb-lg-4">
                        <h3 class="mb-0">
                            Expenses Overview
                        </h3>
                    </div>

                    <div style="margin-top: -25px; margin-left: -10px; margin-bottom: -25px;">
                        <div id="barChartExpenses"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- <div class="row justify-content-center">
        <div class="col-xl-12">
            <div class="card bg-white border-0 rounded-3 mb-4">
                <div class="card-body p-4" style="padding-bottom: 0 !important;">
                    <div class="mb-3 mb-lg-4">
                        <h3 class="mb-0">Financial Overview</h3>
                    </div>
                    <div class="row">
        
                        <div class="col-xxl-6 col-xl-6 col-sm-6">
                            <div
                                class="card bg-primary bg-opacity-10 border-primary border-opacity-10 rounded-3 mb-4 stats-box style-three">
                                <div class="card-body p-4">
                                    <div class="d-flex align-items-center mb-19">
                                        <div class="flex-shrink-0">
                                            <i class="material-symbols-outlined fs-40 text-primary">account_balance_wallet</i>
                                        </div>
                                        <div class="flex-grow-1 ms-2">
                                            <span>Total Balance</span>
                                            <h3 class="fs-20 mt-1 mb-0">
                                                Rp. {{ number_format($totalSaldo, 0, ',', '.') }}
                                            </h3>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-between flex-wrap gap-2 align-items-center">
                                        <span class="fs-12">Remaining this month</span>
                                        <span class="count fw-medium ms-0 {{ $balanceChange < 0 ? 'down' : 'up' }}">
                                            {{ $balanceChange < 0 ? '' : '+' }}{{ number_format($balanceChange, 1) }}% </span>
                                    </div>
                                </div>
                            </div>
                        </div>
        
                        <div class="col-xxl-6 col-xl-6 col-sm-6">
                            <div
                                class="card bg-danger bg-opacity-10 border-danger border-opacity-10 rounded-3 mb-4 stats-box style-three">
                                <div class="card-body p-4">
                                    <div class="d-flex align-items-center mb-19">
                                        <div class="flex-shrink-0">
                                            <i class="material-symbols-outlined fs-40 text-danger">stacks</i>
                                        </div>
                                        <div class="flex-grow-1 ms-2">
                                            <span>Monthly Spending</span>
                                            <h3 class="fs-20 mt-1 mb-0">
                                                Rp. {{ number_format($pengeluaranBulanIni, 0, ',', '.') }}
                                            </h3>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-between flex-wrap gap-2 align-items-center">
                                        <span class="fs-12">Compared to last month</span>
                                        <span class="count fw-medium ms-0 {{ $spendingChange < 0 ? 'up' : 'down' }}">
                                            {{ $spendingChange < 0 ? '' : '+' }}{{ number_format($spendingChange, 1) }}% </span>
                                    </div>
                                </div>
                            </div>
                        </div>
        
                        <div class="col-xxl-12">
                            <div
                                class="card bg-success bg-opacity-10 border-success border-opacity-10 rounded-3 mb-4 stats-box style-three">
                                <div class="card-body p-4">
                                    <div class="d-flex align-items-center mb-19">
                                        <div class="flex-shrink-0">
                                            <i class="material-symbols-outlined fs-40 text-success">attach_money</i>
                                        </div>
                                        <div class="flex-grow-1 ms-2">
                                            <span>Monthly Income</span>
                                            <h3 class="fs-20 mt-1 mb-0">
                                                Rp. {{ number_format($gajiBulanIni, 0, ',', '.') }}
                                            </h3>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-between flex-wrap gap-2 align-items-center">
                                        <span class="fs-12">Compared to last month</span>
                                        <span class="count fw-medium ms-0 {{ $incomeChange < 0 ? 'down' : 'up' }}">
                                            {{ $incomeChange < 0 ? '' : '+' }}{{ number_format($incomeChange, 1) }}% </span>
                                    </div>
                                    <div class="text-muted fs-12 mt-1">
                                        Total income for {{ \Carbon\Carbon::now()->format('F Y') }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xxl-6 col-xl-6 col-sm-6">
                            <div class="card bg-warning bg-opacity-10 border-warning border-opacity-10 rounded-3 mb-4 stats-box style-three">
                                <div class="card-body p-4">
                                    <div class="d-flex align-items-center mb-19">
                                        <div class="flex-shrink-0">
                                            <i class="material-symbols-outlined fs-40 text-warning">calendar_view_week</i>
                                        </div>
                                        <div class="flex-grow-1 ms-2">
                                            <span>Weekly Spending</span>
                                            <h3 class="fs-20 mt-1 mb-0">
                                                Rp. {{ number_format($pengeluaranMingguIni, 0, ',', '.') }}
                                            </h3>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-between flex-wrap gap-2 align-items-center">
                                        <span class="fs-12">Compared to last week</span>
                                        <span class="count fw-medium ms-0 {{ $spendingChange < 0 ? 'up' : 'down' }}">
                                            {{ $spendingChange < 0 ? '' : '+' }}{{ number_format($spendingChange, 1) }}% </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xxl-6 col-xl-6 col-sm-6">
                            <div class="card bg-info bg-opacity-10 border-info border-opacity-10 rounded-3 mb-4 stats-box style-three">
                                <div class="card-body p-4">
                                    <div class="d-flex align-items-center mb-19">
                                        <div class="flex-shrink-0">
                                            <i class="material-symbols-outlined fs-40 text-info">today</i>
                                        </div>
                                        <div class="flex-grow-1 ms-2">
                                            <span>Daily Spending</span>
                                            <h3 class="fs-20 mt-1 mb-0">
                                                Rp. {{ number_format($pengeluaranHariIni, 0, ',', '.') }}
                                            </h3>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-between flex-wrap gap-2 align-items-center">
                                        <span class="fs-12">Compared to yesterday</span>
                                        <span class="count fw-medium ms-0 {{ $dailyChange < 0 ? 'up' : 'down' }}">
                                            {{ $dailyChange < 0 ? '' : '+' }}{{ number_format($dailyChange, 1) }}%
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
        
                    </div>
                </div>
            </div>
        </div>
    </div> --}}
@endsection

@push('after-scripts')
    <script>
        let monthlyExpenses = {!! json_encode($laporanBulananTahunIni) !!};
            console.log(monthlyExpenses);
    
            // Inisialisasi array dengan 12 nol untuk setiap bulan di tahun ini
            let expenseData = Array(12).fill(0);
    
            // Isi data dengan total pengeluaran dari database
            monthlyExpenses.forEach(expense => {
            // Indeks array 0 adalah Januari, sehingga perlu dikurangi 1 dari bulan (1-12)
            expenseData[expense.month - 1] = expense.total;
            });
    
            var options = {
                series: [{
                    data: expenseData
                }],
                chart: {
                    type: 'bar',
                    height: 350
                },
                plotOptions: {
                    bar: {
                        borderRadius: 4,
                        borderRadiusApplication: 'end',
                        horizontal: true,
                    }
                },
                dataLabels: {
                    enabled: false
                },
                tooltip: {
                    y: {
                        title: {
                            formatter: function(val) {
                                return ''
                            }
                        },
                        formatter: function(val) {
                            return 'Rp.' + val.toFixed(0).replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,')
                        }
                    }
                },
                xaxis: {
                    categories: [
                        'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
                    ],
                    labels: {
                        formatter: function(val) {
                            return 'Rp.' + val.toFixed(0).replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,')
                        }
                    }
                }
            };
    
            var chart = new ApexCharts(document.querySelector("#barChartExpenses"), options);
            chart.render();
    </script>
@endpush