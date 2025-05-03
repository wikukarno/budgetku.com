<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use App\Models\CategoryFinance;
use App\Models\Finance;
use App\Models\Portofolio;
use App\Models\Salary;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardAdminController extends Controller
{
    public function index()
    {

        $userId = Auth::user()->id;
        $lastMonth = Carbon::now()->subMonth();

        $pengeluaran = 0;

        $tanggalSemuaGajiBulanKemarinDanBulanIni = Salary::where('users_id', $userId)
            ->whereBetween('date', [$lastMonth->startOfMonth()->format('Y-m-d'), Carbon::now()->endOfMonth()->format('Y-m-d')])
            ->pluck('date')->toArray();

        
        $salary = Salary::where('users_id', $userId)
            ->whereBetween('date', [$lastMonth->startOfMonth()->format('Y-m-d'), Carbon::now()->endOfMonth()->format('Y-m-d')])
            ->sum('salary');


        if (!empty($tanggalSemuaGajiBulanKemarinDanBulanIni)) {
            $pengeluaran = Finance::where('users_id', $userId)
            ->whereBetween('purchase_date', [$tanggalSemuaGajiBulanKemarinDanBulanIni[0], Carbon::now()->endOfMonth()->format('Y-m-d')])
            ->sum('price');
        } else {
            $pengeluaran = 0; 
        }


        $totalPendapatan = $salary - $pengeluaran;

        // monthly report
        $monthlyReport = Finance::where('users_id', Auth::user()->id)
            ->whereMonth('purchase_date', Carbon::now()->format('m'))
            ->sum('price');

        $categoryFinances = CategoryFinance::count();

        $pengeluaranHariIni = Finance::where('users_id', Auth::user()->id)
            ->where('purchase_date', Carbon::now()->format('Y-m-d'))
            ->pluck('price')->toArray();

        $todayExpenditure = array_sum($pengeluaranHariIni);

        $laporanMingguan = Finance::where('users_id', Auth::user()->id)
            ->whereBetween('purchase_date', [Carbon::now()->startOfWeek()->format('Y-m-d'), Carbon::now()->endOfWeek()->format('Y-m-d')])
            ->pluck('price')->toArray();

        $weeklyReport = array_sum($laporanMingguan);

        // Pengeluaran tahun sebelumnya
        $previeusYearReport = Finance::where('users_id', Auth::user()->id)
            ->whereYear('purchase_date', Carbon::now()->subYear()->format('Y'))
            ->sum('price');

        $laporanTahunan = Finance::where('users_id', Auth::user()->id)
            ->whereYear('purchase_date', Carbon::now()->format('Y'))
            ->sum('price');

        $anualReport = Finance::whereYear('purchase_date', Carbon::now()->format('Y'))->sum('price');

        $keterangan = $totalPendapatan <= $monthlyReport ? 'Bulan ' . Carbon::now()->isoFormat('MMMM') . ' Boros Sekali ' . Auth::user()->name . ''  : 'Masih aman kok, jangan lupa investasi dan sedekah ya!';

        $monthlyBills = Bill::where('siklus_tagihan', 0)->sum('harga_tagihan');

        $yearlyBills = Bill::where('siklus_tagihan', 1)->sum('harga_tagihan');


        $laporanBulananTahunIni = Finance::where('users_id', Auth::user()->id)
        ->whereYear('purchase_date', Carbon::now()->format('Y'))
        ->select(DB::raw('MONTH(purchase_date) as month'), DB::raw('sum(price) as total'))
        ->groupBy(DB::raw('MONTH(purchase_date)'))
        ->orderBy('month')
        ->get();

        $kategoriBucin = Finance::where('category_finances_id', 31)->sum('price');

        if (request()->ajax()) {
            $query = Bill::where('siklus_tagihan', 0);

            return datatables()->of($query)
                ->addIndexColumn()
                ->editColumn('harga_tagihan', function ($item) {
                    return 'Rp.' . number_format($item->harga_tagihan, 0, ',', '.');
                })
                ->editColumn('siklus_tagihan', function ($item) {
                    return $item->siklus_tagihan == 0 ? 'Bulanan' : 'Tahunan';
                })
                ->editColumn('metode_pembayaran', function ($item) {
                    return $item->metode_pembayaran == 0 ? 'Cash' : 'Transfer';
                })
                ->editColumn('jatuh_tempo_tagihan', function ($item) {
                    return Carbon::parse($item->jatuh_tempo_tagihan)->isoFormat('D MMMM');
                })
                ->editColumn('action', function ($item) {
                    return '
                        <a href="#" class="btn btn-warning">Edit
                        </a>
                        <a href="javascript:void(0)" onclick="deleteBill(' . $item->id . ')">
                            <button type="button" class="btn btn-danger">Delete</button>
                        </a>
                    ';
                })
                ->rawColumns(['action', 'date', 'salary'])
                ->make(true);
        }

        return view('v2.admin.dashboard', compact(
            'totalPendapatan',
            'pengeluaran',
            'categoryFinances',
            'todayExpenditure',
            'weeklyReport',
            'anualReport',
            'keterangan',
            'monthlyBills',
            'yearlyBills',
            'monthlyReport',
            'previeusYearReport',
            'laporanTahunan',
            'laporanBulananTahunIni',
            'kategoriBucin'
        ));
    }
}
