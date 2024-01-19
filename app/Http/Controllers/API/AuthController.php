<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function fetch(Request $request)
    {
        try {
            $authorization = $request->header('Authorization');
            $user = User::where('id', auth()->user()->id)->first();

            // jika auth_token !== user yang sedang login maka abort 404
            if ($user->auth_token !== request()->bearerToken() && $authorization !== 'Bearer ' . $user->auth_token) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            return response()->json([
                'message' => 'Success',
                'data' => $user
            ], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }

    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'email|required',
                'password' => 'required'
            ]);

            // cek user di database
            $user = User::select('id', 'name', 'email', 'avatar', 'password')->where('email', $request->email)->first();

            // jika user tidak ditemukan
            if (!$user) {
                return response()->json(['message' => 'User not found'], 404);
            }

            // jika password tidak sesuai
            if (!Hash::check($request->password, $user->password)) {
                return response()->json(['message' => 'Password is wrong'], 401);
            }

            // Pembuatan token setelah autentikasi berhasil
            $tokenResult = $user->createToken('auth_token')->plainTextToken;

            // update auth_token user
            $user->auth_token = $tokenResult;
            $user->save();

            return response()->json([
                'access_token' => $tokenResult,
                'token_type' => 'Bearer',
                'user' => $user
            ]);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }

    public function logout()
    {
        try {

            $userToken = User::where('auth_token', request()->bearerToken())->first();

            // jika Authorization header tidak valid
            if (!$userToken) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            $user = User::where('id', auth()->user()->id)->update([
                'auth_token' => null
            ]);

            $cookie = Cookie::forget('auth_token');

            return response()->json([
                'message' => 'Logout success'
            ])->withCookie($cookie->withHttpOnly()->withSecure(true));
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
}
