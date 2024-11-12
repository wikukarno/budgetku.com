<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessUangMasukEmail;
use App\Models\CategoryIncome;
use App\Models\Finance;
use App\Models\Salary;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserIncomeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (request()->ajax()) {
            $query = Salary::with('category_income')
                ->where('users_id', Auth::id())
                ->orderBy('created_at', 'DESC');

            return datatables()->of($query)
                ->addIndexColumn()
                ->editColumn('tipe', function ($item) {
                    return $item->category_income->name_category_incomes;
                })
                ->editColumn('salary', function ($item) {
                    return 'Rp.' . number_format($item->salary, 0, ',', '.');
                })
                ->editColumn('date', function ($item) {
                    return Carbon::parse($item->date)->isoFormat('D MMMM Y');
                })
                ->editColumn('action', function ($item) {
                    return '
                        <a href="' . route('income.edit', $item->id) . '" class="btn btn-sm btn-warning">
                            Edit
                        </a>
                        <a href="javascript:void(0)" class="btn btn-sm btn-danger" onclick="deleteSalary(' . $item->id . ')">
                            Delete
                        </a>
                    ';
                })
                ->rawColumns(['action', 'date', 'salary', 'tipe'])
                ->make(true);
        }

        
        return view('user.salary.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categoryIncome = CategoryIncome::where('users_id', Auth::id())->get();
        return view('user.salary.create', compact('categoryIncome'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = Salary::create([
            'users_id' => Auth::id(),
            'salary' => str_replace(
                ['Rp. ', '.'],
                ['', ''],
                $request->salary
            ),
            'date' => $request->date,
            'tipe' => $request->tipe,
            'description' => $request->description,
        ]);

        $email = User::where('email', Auth::user()->email)->first();

        $data = [
            'salary' => $data,
            'user' => $email
        ];

        ProcessUangMasukEmail::dispatch(
            $data
        );

        return to_route('income.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $data = Salary::find($request->id);
        return response()->json($data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = Salary::where('users_id', Auth::id())->findOrFail($id);
        $categoryIncome = CategoryIncome::where('users_id', Auth::id())
            ->get();
        return view('user.salary.edit', compact('data', 'categoryIncome'));
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
        try {
            DB::transaction(function () use ($id, $request) {
                $data = Salary::findOrFail($id); // Gunakan findOrFail untuk memastikan data ditemukan
                $this->authorize('update', $data);

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

                // kalau update tanggal melebihi tanggal sekarang maka akan error
                if ($request->date > Carbon::now()->format('Y-m-d')) {
                    Log::error('Tanggal tidak boleh melebihi tanggal sekarang');
                    return false;
                }

                // Update fields
                $data->users_id = Auth::user()->id;
                $data->salary = str_replace(
                    ['Rp.', '.'],
                    ['', ''],
                    $request->salary
                );
                $data->date = $request->date;
                $data->tipe = $request->tipe;
                $data->description = $request->description;
                $data->save();

                $totalPendapatan = $salary - $pengeluaran;

                // update saldo
                $user = User::find(Auth::id());
                $user->saldo = $totalPendapatan;
                $user->save();

            });

            return redirect()->route('income.index');
        } catch (\Throwable $th) {
            return redirect()->route('income.index')->withErrors(['error' => $th->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        try {
            $data = Salary::find($request->id);
            $this->authorize('delete', $data);
            $data->delete();

            // Update saldo
            $user = User::find(Auth::id());
            $user->saldo -= $data->salary;
            $user->save();

            return response()->json([
                'code' => 200,
                'message' => 'Data berhasil dihapus'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'code' => 500,
                'message' => 'Data gagal dihapus'
            ]);
        }
    }
}
