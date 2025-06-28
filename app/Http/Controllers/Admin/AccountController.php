<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use PragmaRX\Google2FAQRCode\Google2FA;

class AccountController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = User::where('id', Auth::user()->id)->first();
        $qrCode = null;
        $secret = null;

        if ($user->two_factor_secret) {
            $google2fa = new Google2FA();
            $secret = Crypt::decrypt($user->two_factor_secret);

            $svg = $google2fa->getQRCodeInline(
                'budgetku.com',
                $user->email,
                $secret
            );

            $qrCode = 'data:image/svg+xml;base64,' . base64_encode($svg);
        }
        return view('v2.admin.account', compact('user', 'qrCode', 'secret'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return abort(404);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return abort(404);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return abort(404);
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
    public function update(Request $request)
    {
        if (Auth::user()->id) {
            $user = User::where('id', Auth::user()->id)->first();
            $user->name = $request->name;
            $user->email_parrent = $request->email_parrent;
            $user->notifications = $request->notifications;
            $user->save();
            return back();
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

    public function ubahProfile(Request $request)
    {
        $user = User::findOrfail(Auth::user()->id);
        $user->avatar = $request->file('avatar')->storePubliclyAs('assets/avatar', $request->file('avatar')->getClientOriginalName(), 'public');
        $user->save();
        return back();
    }
}
