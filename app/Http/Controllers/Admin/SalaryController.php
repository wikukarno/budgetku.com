<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SalaryRequest;
use App\Jobs\ProcessUangMasukEmail;
use App\Models\Salary;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SalaryController extends Controller
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
                        <a href="javascript:void(0)" onclick="updateSalary(' . $item->id . ')">
                            <button type="button" class="btn btn-warning">Edit</button>
                        </a>
                        <a href="javascript:void(0)" onclick="deleteSalary(' . $item->id . ')">
                            <button type="button" class="btn btn-danger">Delete</button>
                        </a>
                    ';
                })
                ->rawColumns(['action', 'date', 'salary'])
                ->make(true);
        }

        return view('admin.salary.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SalaryRequest $request)
    {
        $data = Salary::updateOrCreate(
            ['id' => $request->id_salary],
            [
                'users_id' => Auth::user()->id,
                'salary' => str_replace(
                    ['Rp. ', '.'],
                    ['', ''],
                    $request->salary
                ),
                'date' => $request->date,
                'description' => $request->description,
            ]
        );

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
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $data = Salary::find($request->id);
        $data->delete();

        if ($data) {
            return redirect()->route('salary.index');
        } else {
            return redirect()->route('salary.index');
        }
    }
}
