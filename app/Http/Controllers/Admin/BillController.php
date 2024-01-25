<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\BillRequest;
use App\Models\Bill;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BillController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (request()->ajax()) {
            $query = Bill::with('user')->where('users_id', auth()->user()->id)->orderBy('created_at', 'DESC');

            return datatables()->of($query)
                ->addIndexColumn()
                ->editColumn('pemilik_tagihan', function ($item) {
                    return $item->user->name;
                })
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
                        <a href="'. route('bill.edit', $item->id) .'" class="btn btn-warning">Edit
                        </a>
                        <a href="javascript:void(0)" onclick="deleteBill(' . $item->id . ')">
                            <button type="button" class="btn btn-danger">Delete</button>
                        </a>
                    ';
                })
                ->rawColumns(['action', 'date', 'salary'])
                ->make(true);
        }

        return view('admin.bill.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.bill.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(BillRequest $request)
    {
        Bill::create([
            'nama_tagihan' => $request->nama_tagihan,
            'harga_tagihan' => str_replace(
                ['Rp', '.'], ['', ''], 
                $request->harga_tagihan
            ),
            'pemilik_tagihan' => $request->pemilik_tagihan,
            'siklus_tagihan' => $request->siklus_tagihan,
            'jatuh_tempo_tagihan' => $request->jatuh_tempo_tagihan,
            'metode_pembayaran' => $request->metode_pembayaran,
            'keterangan_tagihan' => $request->keterangan_tagihan,
        ]);

        return redirect()->route('bill.index')->with('success', 'Data berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = Bill::findOrFail($id);
        return view('admin.bill.show', compact('data'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $item = Bill::findOrFail($id);
        return view('admin.bill.edit', compact('item'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $data = Bill::findOrFail($id);
        $data->update([
            'nama_tagihan' => $request->nama_tagihan,
            'harga_tagihan' => str_replace(
                ['Rp', '.'],
                ['', ''],
                $request->harga_tagihan
            ),
            'pemilik_tagihan' => $request->pemilik_tagihan,
            'siklus_tagihan' => $request->siklus_tagihan,
            'jatuh_tempo_tagihan' => $request->jatuh_tempo_tagihan,
            'metode_pembayaran' => $request->metode_pembayaran,
            'keterangan_tagihan' => $request->keterangan_tagihan,
        ]);

        return redirect()->route('bill.index')->with('success', 'Data berhasil diubah');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $data = Bill::findOrFail($request->id);
        $data->delete();

        return redirect()->route('bill.index')->with('success', 'Data berhasil dihapus');
    }
}
