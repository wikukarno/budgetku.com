<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessUangMasukEmail;
use App\Models\Salary;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
            $query = Salary::query();

            return datatables()->of($query)
                ->addIndexColumn()
                ->editColumn('salary', function ($item) {
                    return 'Rp.' . number_format($item->salary, 0, ',', '.');
                })
                ->editColumn('date', function ($item) {
                    return Carbon::parse($item->date)->isoFormat('D MMMM Y');
                })
                ->editColumn('action', function ($item) {
                    return '
                        <a href="' . route('salary.edit', $item->id) . '" class="btn btn-warning">
                            Edit
                        </a>
                        <a href="javascript:void(0)" onclick="deleteSalary(' . $item->id . ')">
                            <button type="button" class="btn btn-danger">Delete</button>
                        </a>
                    ';
                })
                ->rawColumns(['action', 'date', 'salary'])
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
        return view('user.salary.create');
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
            'users_id' => Auth::user()->id,
            'salary' => str_replace(
                ['Rp. ', '.'],
                ['', ''],
                $request->salary
            ),
            'date' => $request->date,
            'tipe' => $request->tipe,
            'description' => $request->description,
        ]);

        $user = User::where('email', Auth::user()->email)->firstOrFail();

        $data = [
            'salary' => $data,
            'user' => $user
        ];
        ProcessUangMasukEmail::dispatch(
            $data
        );

        if ($data) {
            return redirect()->route('salary.index');
        } else {
            return redirect()->route('salary.index');
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
        $data = Salary::find($id);
        return view('user.salary.edit', [
            'data' => $data
        ]);
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
            $data = Salary::find($id);
            $this->authorize('update', $data);
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

            return redirect()->route('salary.index');
        } catch (\Throwable $th) {
            return redirect()->route('salary.index');
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
