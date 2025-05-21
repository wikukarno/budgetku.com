<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Finance;
use App\Services\DashboardCacheService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardCustomerController extends Controller
{

    public function index(DashboardCacheService $dashboard)
    {
        $gajiBulanIni = $dashboard->gajiBulanIni();
        $gajiBulanLalu = $dashboard->gajiBulanLalu();
        $pengeluaranBulanIni = $dashboard->pengeluaranBulanIni();
        $pengeluaranBulanLalu = $dashboard->pengeluaranBulanLalu();
        $laporanBulananTahunIni = $dashboard->laporanBulananTahunIni();
        $totalSaldo = $dashboard->totalSaldo();

        // Yang real-time (mingguan/harian) bisa tanpa cache
        $userId = Auth::id();
        $pengeluaranMingguIni = Finance::where('users_id', $userId)
            ->whereBetween('purchase_date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
            ->sum('price');

        $pengeluaranMingguLalu = Finance::where('users_id', $userId)
            ->whereBetween('purchase_date', [Carbon::now()->subWeek()->startOfWeek(), Carbon::now()->subWeek()->endOfWeek()])
            ->sum('price');

        $pengeluaranKemarin = Finance::where('users_id', $userId)
            ->whereDate('purchase_date', Carbon::yesterday())
            ->sum('price');

        $pengeluaranHariIni = Finance::where('users_id', $userId)
            ->whereDate('purchase_date', Carbon::today())
            ->sum('price');

        $dailyChange = $pengeluaranKemarin > 0 ? (($pengeluaranHariIni - $pengeluaranKemarin) / $pengeluaranKemarin) * 100 : 0;
        $weeklyChange = $pengeluaranMingguLalu > 0 ? (($pengeluaranMingguIni - $pengeluaranMingguLalu) / $pengeluaranMingguLalu) * 100 : 0;
        $saldoBulanIni = $gajiBulanIni - $pengeluaranBulanIni;
        $saldoBulanLalu = $gajiBulanLalu - $pengeluaranBulanLalu;
        $incomeChange = $gajiBulanLalu > 0 ? (($gajiBulanIni - $gajiBulanLalu) / $gajiBulanLalu) * 100 : 0;
        $spendingChange = $pengeluaranBulanLalu > 0 ? (($pengeluaranBulanIni - $pengeluaranBulanLalu) / $pengeluaranBulanLalu) * 100 : 0;
        $balanceChange = $saldoBulanLalu > 0 ? (($saldoBulanIni - $saldoBulanLalu) / $saldoBulanLalu) * 100 : 0;

        return view('v2.user.dashboard', compact(
            'gajiBulanIni',
            'gajiBulanLalu',
            'pengeluaranBulanIni',
            'pengeluaranBulanLalu',
            'laporanBulananTahunIni',
            'pengeluaranMingguIni',
            'pengeluaranHariIni',
            'pengeluaranKemarin',
            'pengeluaranMingguLalu',
            'dailyChange',
            'weeklyChange',
            'saldoBulanIni',
            'spendingChange',
            'balanceChange',
            'incomeChange',
            'totalSaldo'
        ));
    }

    // public function index()
    // {
    //     $userId = Auth::id();

    //     // === Gaji (Income) ===
    //     $gajiBulanIni = Salary::where('users_id', $userId)
    //         ->whereMonth('date', Carbon::now()->month)
    //         ->whereYear('date', Carbon::now()->year)
    //         ->sum('salary');

    //     $gajiBulanLalu = Salary::where('users_id', $userId)
    //         ->whereMonth('date', Carbon::now()->subMonth()->month)
    //         ->whereYear('date', Carbon::now()->subMonth()->year)
    //         ->sum('salary');

    //     // === Pengeluaran (Spending) ===
    //     $pengeluaranBulanIni = Finance::where('users_id', $userId)
    //         ->whereMonth('purchase_date', Carbon::now()->month)
    //         ->whereYear('purchase_date', Carbon::now()->year)
    //         ->sum('price');

    //     $pengeluaranBulanLalu = Finance::where('users_id', $userId)
    //         ->whereMonth('purchase_date', Carbon::now()->subMonth()->month)
    //         ->whereYear('purchase_date', Carbon::now()->subMonth()->year)
    //         ->sum('price');

    //     $laporanBulananTahunIni = Finance::where('users_id', Auth::id())
    //         ->whereYear('purchase_date', Carbon::now()->year)
    //         ->selectRaw('MONTH(purchase_date) as month, SUM(price) as total')
    //         ->groupByRaw('MONTH(purchase_date)')
    //         ->orderBy('month')
    //         ->get();

    //     $pengeluaranMingguLalu = Finance::where('users_id', Auth::id())
    //         ->whereBetween('purchase_date', [
    //             Carbon::now()->subWeek()->startOfWeek(),
    //             Carbon::now()->subWeek()->endOfWeek()
    //         ])
    //         ->sum('price');

    //     $pengeluaranMingguIni = Finance::where('users_id', Auth::id())
    //         ->whereBetween('purchase_date', [
    //             Carbon::now()->startOfWeek(),
    //             Carbon::now()->endOfWeek()
    //         ])
    //         ->sum('price');

    //     $pengeluaranKemarin = Finance::where('users_id', Auth::id())
    //         ->whereDate('purchase_date', Carbon::yesterday())
    //         ->sum('price');

    //     $pengeluaranHariIni = Finance::where('users_id', Auth::id())
    //         ->whereDate('purchase_date', Carbon::today())
    //         ->sum('price');

    //     // === Daily Change ===
    //     $dailyChange = $pengeluaranKemarin > 0
    //         ? (($pengeluaranHariIni - $pengeluaranKemarin) / $pengeluaranKemarin) * 100
    //         : 0;

    //     // === Weekly Change ===
    //     $weeklyChange = $pengeluaranMingguLalu > 0
    //         ? (($pengeluaranMingguIni - $pengeluaranMingguLalu) / $pengeluaranMingguLalu) * 100
    //         : 0;

    //     // === Saldo Bulanan ===
    //     $saldoBulanIni = $gajiBulanIni - $pengeluaranBulanIni;
    //     $saldoBulanLalu = $gajiBulanLalu - $pengeluaranBulanLalu;

    //     // === Perubahan Persentase Income ===
    //     $incomeChange = $gajiBulanLalu > 0
    //         ? (($gajiBulanIni - $gajiBulanLalu) / $gajiBulanLalu) * 100
    //         : 0;

    //     // === Perubahan Persentase Spending ===
    //     $spendingChange = $pengeluaranBulanLalu > 0
    //         ? (($pengeluaranBulanIni - $pengeluaranBulanLalu) / $pengeluaranBulanLalu) * 100
    //         : 0;

    //     // === Perubahan Persentase Saldo ===
    //     $balanceChange = $saldoBulanLalu > 0
    //         ? (($saldoBulanIni - $saldoBulanLalu) / $saldoBulanLalu) * 100
    //         : 0;

    //     // === Total saldo akumulatif (lifetime) ===
    //     $totalSalary = Salary::where('users_id', $userId)->sum('salary');
    //     $totalSpending = Finance::where('users_id', $userId)->sum('price');
    //     $totalSaldo = $totalSalary - $totalSpending;

    //     return view('v2.user.dashboard', compact(
    //         'gajiBulanIni',
    //         'gajiBulanLalu',
    //         'pengeluaranBulanIni',
    //         'pengeluaranBulanLalu',
    //         'laporanBulananTahunIni',
    //         'pengeluaranMingguIni',
    //         'pengeluaranHariIni',
    //         'pengeluaranKemarin',
    //         'pengeluaranMingguLalu',
    //         'dailyChange',
    //         'weeklyChange',
    //         'saldoBulanIni',
    //         'spendingChange',
    //         'balanceChange',
    //         'incomeChange',
    //         'totalSaldo'
    //     ));
    // }

    // Old code
    // public function index()
    // {
    //     $userId = Auth::user()->id;

    //     $salary = Salary::where('users_id', $userId)
    //         ->sum('salary');

    //     $pengeluaran = Finance::where('users_id', $userId)
    //         ->sum('price');

    //     $pengeluaran_bulan_berjalan = Finance::where('users_id', $userId)
    //         ->where('purchase_date', '>', Carbon::now()->subMonth()->format('Y-m-d'))
    //         ->sum('price');

    //     $saldo = $salary - $pengeluaran;

    //     // monthly report
    //     $monthlyReport = Finance::where('users_id', Auth::user()->id)
    //         ->whereMonth('purchase_date', Carbon::now()->format('m'))
    //         ->sum('price');

    //     $categoryFinances = CategoryFinance::count();

    //     $pengeluaranHariIni = Finance::where('users_id', Auth::user()->id)
    //         ->where('purchase_date', Carbon::now()->format('Y-m-d'))
    //         ->pluck('price')->toArray();

    //     $todayExpenditure = array_sum($pengeluaranHariIni);

    //     $laporanMingguan = Finance::where('users_id', Auth::user()->id)
    //         ->whereBetween('purchase_date', [Carbon::now()->startOfWeek()->format('Y-m-d'), Carbon::now()->endOfWeek()->format('Y-m-d')])
    //         ->pluck('price')->toArray();

    //     $weeklyReport = array_sum($laporanMingguan);

    //     // Pengeluaran tahun sebelumnya
    //     $previeusYearReport = Finance::where('users_id', Auth::user()->id)
    //         ->whereYear('purchase_date', Carbon::now()->subYear()->format('Y'))
    //         ->sum('price');

    //     $laporanTahunan = Finance::where('users_id', Auth::user()->id)
    //         ->whereYear('purchase_date', Carbon::now()->format('Y'))
    //         ->sum('price');

    //     $anualReport = Finance::where('users_id', AUth::id())->whereYear('purchase_date', Carbon::now()->format('Y'))->sum('price');

    //     $keterangan = $saldo <= $monthlyReport ? 'Bulan ' . Carbon::now()->isoFormat('MMMM') . ' Boros Sekali ' . Auth::user()->name . ''  : 'Masih aman kok, jangan lupa investasi dan sedekah ya!';

    //     $monthlyBills = Bill::where('siklus_tagihan', 0)->sum('harga_tagihan');

    //     $yearlyBills = Bill::where('siklus_tagihan', 1)->sum('harga_tagihan');


    //     $laporanBulananTahunIni = Finance::where('users_id', Auth::user()->id)
    //     ->whereYear('purchase_date', Carbon::now()->format('Y'))
    //     ->select(DB::raw('MONTH(purchase_date) as month'), DB::raw('sum(price) as total'))
    //     ->groupBy(DB::raw('MONTH(purchase_date)'))
    //     ->orderBy('month')
    //     ->get();

    //     $kategoriBucin = Finance::where('category_finances_id', 31)->sum('price');

    //     $totalPreviousYear = Finance::where('users_id', Auth::id()) // Dapatkan data user saat ini
    //     ->whereYear('purchase_date', Carbon::now()->subYear()->year) // Filter tahun sebelumnya
    //     ->sum('price'); // Hitung total pengeluaran

    //     $pengeluaranBulanSebelumnya = Finance::where('users_id', $userId)
    //         ->whereMonth('purchase_date', Carbon::now()->subMonth()->format('m'))
    //         ->whereYear('purchase_date', Carbon::now()->subMonth()->format('Y'))
    //         ->sum('price');

    //     $spendingChange = $pengeluaranBulanSebelumnya > 0
    //         ? (($monthlyReport - $pengeluaranBulanSebelumnya) / $pengeluaranBulanSebelumnya) * 100
    //         : 100;

    //     $saldoBulanLalu = $salary - $pengeluaranBulanSebelumnya;
    //     $saldoBulanIni = $salary - $monthlyReport;

    //     $balanceChange = $saldoBulanLalu > 0
    //         ? (($saldoBulanIni - $saldoBulanLalu) / $saldoBulanLalu) * 100
    //         : 100;

    //     if (request()->ajax()) {
    //         $query = Bill::where('siklus_tagihan', 0);

    //         return datatables()->of($query)
    //             ->addIndexColumn()
    //             ->editColumn('harga_tagihan', function ($item) {
    //                 return 'Rp.' . number_format($item->harga_tagihan, 0, ',', '.');
    //             })
    //             ->editColumn('siklus_tagihan', function ($item) {
    //                 return $item->siklus_tagihan == 0 ? 'Bulanan' : 'Tahunan';
    //             })
    //             ->editColumn('metode_pembayaran', function ($item) {
    //                 return $item->metode_pembayaran == 0 ? 'Cash' : 'Transfer';
    //             })
    //             ->editColumn('jatuh_tempo_tagihan', function ($item) {
    //                 return Carbon::parse($item->jatuh_tempo_tagihan)->isoFormat('D MMMM');
    //             })
    //             ->editColumn('action', function ($item) {
    //                 return '
    //                     <a href="' . route('bill.edit', $item->id) . '" class="btn btn-warning">Edit
    //                     </a>
    //                     <a href="javascript:void(0)" onclick="deleteBill(' . $item->id . ')">
    //                         <button type="button" class="btn btn-danger">Delete</button>
    //                     </a>
    //                 ';
    //             })
    //             ->rawColumns(['action', 'date', 'salary'])
    //             ->make(true);
    //     }

    //     return view('v2.user.dashboard', compact(
    //         'saldo',
    //         'pengeluaran',
    //         'pengeluaran_bulan_berjalan',
    //         'categoryFinances',
    //         'todayExpenditure',
    //         'weeklyReport',
    //         'anualReport',
    //         'keterangan',
    //         'monthlyBills',
    //         'yearlyBills',
    //         'monthlyReport',
    //         'previeusYearReport',
    //         'laporanTahunan',
    //         'laporanBulananTahunIni',
    //         'kategoriBucin',
    //         'totalPreviousYear',
    //         'spendingChange',
    //         'balanceChange'
    //     ));
    // }
}
