<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserAccountController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = User::where('uuid', Auth::id())->first();
        return view('v2.user.account', compact('user'));
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
    public function update(Request $request, $id)
    {
        try {
            $user = User::where('uuid', $id)->first();
            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'User not found'
                ], 404);
            }

            if (Auth::id() !== $user->uuid) {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthorized'
                ], 403);
            }

            $user->name = $request->name;
            $user->email_parrent = $request->email_parrent ?? null;
            $user->save();

            return response()->json([
                'status' => true,
                'message' => 'Profile updated successfully'
            ]);
        } catch (\Throwable $e) {
            Log::error('Update Account Error: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Failed to update profile'
            ]);
        }
    }

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

    public function ubahProfile(Request $request)
    {
        $user = User::findOrfail(Auth::id());
        $user->avatar = $request->file('avatar')->store('assets/avatar', 'public');
        $user->save();
        return back();
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
}
