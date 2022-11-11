<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PortofolioRequest;
use App\Models\Portofolio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\File;

class PortofoliosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $portofolios = Portofolio::all();
        return view('admin.portofolio.index', compact('portofolios'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.portofolio.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PortofolioRequest $request)
    {
        Portofolio::create([
            'thumbnail' => $request->file('thumbnail')->storePubliclyAs('assets/portofolio', $request->file('thumbnail')->getClientOriginalName(), 'public'),
            'title' => $request->title,
            'url' => $request->url,
            'kategori' => $request->kategori,
            'published' => $request->published
        ]);

        return redirect()->route('portofolio.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
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
        $portofolio = Portofolio::findOrFail($id);
        return view('admin.portofolio.edit', compact('portofolio'));
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
            $data = Portofolio::findOrFail($id);
            Validator::make($request->all(), [
                'thumbnail' => ['image', File::image()->min(1024)->max(12 * 1024), 'mimes:jpeg,jpg,png'],
                'title' => ['required'],
                'url' => ['required']
            ]);
            $data->title = $request->title;
            $data->url = $request->url;
            $data->kategori = $request->kategori;
            $data->published = $request->published;
            if ($request->file('thumbnail')) {
                Storage::delete($data->thumbnail);
                $data->thumbnail = $request->file('thumbnail')->store('assets/portofolio', 'public');
            }
            $data->save();
            return redirect()->route('portofolio.index');
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
        $portofolio = Portofolio::findOrFail($id);
        Storage::delete($portofolio->thumbnail);
        $portofolio->delete();
        return redirect()->route('portofolio.index');
    }
}
