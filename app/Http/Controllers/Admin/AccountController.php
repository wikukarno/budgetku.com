<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
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
        try {
            $user = User::where('id', Auth::user()->id)->first();
            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'User not found'
                ], 404);
            }

            $user->name = $request->name;
            $user->email_parrent = $request->email_parrent ?? null;
            $user->notifications = $request->has('notifications') ? true : false;
            $user->save();

            return response()->json([
                'status' => true,
                'message' => 'Profile updated successfully'
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to update profile'
            ]);
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
            $user = Auth::user();

            // Optional: Backup or archive logic here

            Auth::logout();
            $user->delete();

            return response()->json([
                'status' => true,
                'message' => 'Your account has been deleted successfully.'
            ]);
        } catch (\Throwable $th) {
            Log::error('Account deletion error: ' . $th->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Failed to delete account'
            ]);
        }
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed'
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'status' => false,
                'message' => 'Current password is incorrect'
            ]);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json([
            'status' => true,
            'message' => 'Password updated successfully'
        ]);
    }

    public function ubahProfile(Request $request)
    {
        $user = User::findOrfail(Auth::user()->id);
        $user->avatar = $request->file('avatar')->storePubliclyAs('assets/avatar', $request->file('avatar')->getClientOriginalName(), 'public');
        $user->save();
        return back();
    }
}
