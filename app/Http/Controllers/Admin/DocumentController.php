<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class DocumentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (request()->ajax()) {
            $query = Document::query();

            return datatables()->of($query)
                ->addIndexColumn()
                ->editColumn('file', function ($item) {
                    return '<a href="' . Storage::url($item->file) . '" target="_blank" class="badge bg-success">Download</a>';
                })
                ->editColumn('action', function ($item) {
                    return '
                        <a href="' . route('document.edit', $item->id) . '">
                            <button type="button" class="btn btn-warning">Edit</button>
                        </a>
                    ';
                })
                ->rawColumns(['action', 'file'])
                ->make(true);
        }
        return view('admin.document.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.document.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // if ($request->hasFile('file')) {
        //     $file = $request->file('file');
        //     $fileName = $file->getClientOriginalName();
        //     $file->move('storage/assets/document', $fileName);

        Document::create([
            'name' => $request->name,
            'file' => $request->file('file')->storeAs('assets/document', $request->file('file')->getClientOriginalName(), 'public'),
        ]);
        // }

        return redirect()->route('document.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $item = Document::findOrFail($id);

        return view('admin.document.edit', compact('item'));
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
        if (Auth::user()->id) {
            $data = Document::findOrFail($id);
            Validator::make($request->all(), [
                'name' => ['required'],
                'file' => ['pdf', 'mimes:pdf', 'max:2048']
            ]);
            $data->name = $request->name;

            if ($request->hasFile('file')) {
                Storage::delete('assets/document/' . $data->file);
                $data->file = $request->file('file')->storeAs('assets/document', $request->file('file')->getClientOriginalName(), 'public');
            }


            $data->save();
            return redirect()->route('document.index');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
