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

class DashboardAdminController extends Controller
{
    public function index()
    {
        $portofolios = Portofolio::count();

        $tanggalGajiBulanKemarin = Salary::where('users_id', Auth::user()->id)
            ->whereMonth('date', Carbon::now()->subMonth()->format('m'))->first()->date;

        // ambil data gaji bulan kemarin
        $salary = Salary::where('users_id', Auth::user()->id)
            ->whereMonth('date', Carbon::now()->subMonth()->format('m'))->sum('salary');

        // pengeluara bulan ini dan bulan kemarin
        $pengeluaran = Finance::where('users_id', Auth::user()->id)
            ->where('purchase_date', '>=', $tanggalGajiBulanKemarin)->sum('price');

        // sisa gaji bulan kemarin - pengeluaran bulan ini
        $sisaGaji = $salary - $pengeluaran;

        // monthly report
        $monthlyReport = Finance::where('users_id', Auth::user()->id)
            ->where('purchase_date', '>=' , $tanggalGajiBulanKemarin)->sum('price');

        $categoryFinances = CategoryFinance::count();

        $pengeluaranHariIni = Finance::where('users_id', Auth::user()->id)
            ->where('purchase_date', Carbon::now()->format('Y-m-d'))
            ->pluck('price')->toArray();

        $todayExpenditure = array_sum($pengeluaranHariIni);

        $laporanMingguan = Finance::where('users_id', Auth::user()->id)
            ->whereBetween('purchase_date', [Carbon::now()->startOfWeek()->format('Y-m-d'), Carbon::now()->endOfWeek()->format('Y-m-d')])
            ->pluck('price')->toArray();

        $weeklyReport = array_sum($laporanMingguan);

        $laporanTahunan = Finance::where('users_id', Auth::user()->id)
            ->whereYear('purchase_date', Carbon::now()->format('Y'))
            ->pluck('price')->toArray();

        $anualReport = array_sum($laporanTahunan);

        $anualReport = Finance::whereYear('purchase_date', Carbon::now()->format('Y'))->sum('price');

        $keterangan = $sisaGaji <= $monthlyReport ? 'Bulan ' . Carbon::now()->isoFormat('MMMM') . ' Boros Sekali ' . Auth::user()->name . ''  : 'Masih aman kok, jangan lupa investasi dan sedekah ya!';

        $monthlyBills = Bill::where('siklus_tagihan', 0)->sum('harga_tagihan');

        $yearlyBills = Bill::where('siklus_tagihan', 1)->sum('harga_tagihan');


        return view('admin.dashboard', compact(
            'portofolios',
            // 'gajiSekarang',
            'sisaGaji',
            'pengeluaran',
            'categoryFinances',
            'todayExpenditure',
            'weeklyReport',
            'anualReport',
            'keterangan',
            'monthlyBills',
            'yearlyBills',
            // 'tanggalBulanIni',
            'tanggalGajiBulanKemarin',
            'monthlyReport'
        ));
    }
}
