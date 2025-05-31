<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessUangMasukEmail;
use App\Models\CategoryIncome;
use App\Models\Salary;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class UserIncomeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        if (request()->ajax()) {
            $query = Salary::with('category_income')
                ->where('users_uuid', Auth::id())
                ->orderBy('created_at', 'DESC');

            return datatables()->of($query)
                ->addIndexColumn()
                ->editColumn('category_incomes_uuid', function ($item) {
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
                        <a href="' . route('customer.income.edit', $item->uuid) . '" class="btn btn-sm btn-warning text-white">
                            Edit
                        </a>
                        <a href="javascript:void(0)" class="btn btn-sm btn-danger text-white" onclick="deleteIncome(\'' . $item->uuid . '\')">
                            Delete
                        </a>
                    ';
                })
                ->rawColumns(['action', 'date', 'salary', 'tipe'])
                ->make(true);
        }

        return view('v2.user.income.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // $categoryIncome = CategoryIncome::where('users_uuid', Auth::id())->get();
        $categoryIncome = Cache::remember('user_categories_income_' . Auth::id(), 3600, function () {
            return CategoryIncome::where('users_uuid', Auth::id())->get();
        });

        return view('v2.user.income.create', compact('categoryIncome'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'salary' => 'required|string',
            'date' => 'required|date',
            'category_incomes_uuid' => 'required|exists:category_incomes,uuid',
            'description' => 'required|string',
        ]);

        try {
            $salary = Salary::create([
                'users_uuid' => Auth::id(),
                'salary' => str_replace(['Rp. ', '.'], ['', ''], $request->salary),
                'date' => $request->date,
                'category_incomes_uuid' => $request->category_incomes_uuid,
                'description' => $request->description,
            ]);

            // Clear cache
            Cache::forget('gaji_bulan_ini_user_' . Auth::id());
            Cache::forget('gaji_bulan_lalu_user_' . Auth::id());
            Cache::forget('pengeluaran_bulan_ini_user_' . Auth::id());
            Cache::forget('pengeluaran_bulan_lalu_user_' . Auth::id());
            Cache::forget('laporan_tahunan_user_' . Auth::id());

            // Send email
            $user = Auth::user(); // already available
            $data = [
                'salary' => $salary,
                'user' => $user
            ];

            ProcessUangMasukEmail::dispatch($data);

            return response()->json([
                'status' => true,
                'message' => 'Data Created Successfully',
            ]);
        } catch (\Exception $exception) {
            Log::error('Error during salary submission: ' . $exception->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Data Failed to Create',
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $data = Salary::find($request->uuid);
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
        $data = Salary::where('users_uuid', Auth::id())->findOrFail($id);

        $categoryIncome = Cache::remember('user_categories_income_' . Auth::id(), 3600, function () {
            return CategoryIncome::where('users_uuid', Auth::id())->get();
        });

        $data->salary = 'Rp. ' . number_format($data->salary, 0, ',', '.');
        return view('v2.user.income.edit', compact('data', 'categoryIncome'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'salary' => 'required|string',
            'date' => 'required|date',
            'category_incomes_uuid' => 'required|exists:category_incomes,uuid',
            'description' => 'required|string',
        ]);

        try {
            $data = Salary::findOrFail($id); // Gunakan findOrFail untuk memastikan data ditemukan
            $this->authorize('update', $data);

            // kalau update tanggal melebihi tanggal sekarang maka akan error
            if ($request->date > Carbon::now()->format('Y-m-d')) {
                Log::error('Tanggal tidak boleh melebihi tanggal sekarang');
                return false;
            }

            // Update fields
            $data->users_uuid = Auth::id();
            $data->salary = str_replace(
                ['Rp.', '.'],
                ['', ''],
                $request->salary
            );
            $data->date = $request->date;
            $data->category_incomes_uuid = $request->category_incomes_uuid;
            $data->description = $request->description;
            $data->save();

            // Delete cache
            Cache::forget('gaji_bulan_ini_user_' . Auth::id());
            Cache::forget('gaji_bulan_lalu_user_' . Auth::id());
            Cache::forget('pengeluaran_bulan_ini_user_' . Auth::id());
            Cache::forget('pengeluaran_bulan_lalu_user_' . Auth::id());
            Cache::forget('laporan_tahunan_user_' . Auth::id());

            return response()->json([
                'status' => true,
                'message' => 'Data Updated Successfully',
            ]);
        } catch (\Throwable $th) {
            Log::error('Error updating data: ' . $th->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Data Failed to Update',
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request)
    {
        try {
            $data = Salary::find($request->uuid);
            $this->authorize('delete', $data);
            $data->delete();

            // Delete cache
            Cache::forget('gaji_bulan_ini_user_' . Auth::id());
            Cache::forget('gaji_bulan_lalu_user_' . Auth::id());
            Cache::forget('pengeluaran_bulan_ini_user_' . Auth::id());
            Cache::forget('pengeluaran_bulan_lalu_user_' . Auth::id());
            Cache::forget('laporan_tahunan_user_' . Auth::id());
            Cache::forget('user_categories_income_' . Auth::id());

            return response()->json([
                'code' => 200,
                'message' => 'Data successfully deleted',
            ]);
        } catch (\Throwable $th) {
            Log::error('Error deleting data: ' . $th->getMessage());
            return response()->json([
                'code' => 500,
                'message' => 'Data Failed to Delete',
            ]);
        }
    }
}
