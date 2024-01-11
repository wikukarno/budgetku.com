<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;

class ApiToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $token = $request->header('Authorization');

        // Pertama, periksa apakah token ada
        if (!$token) {
            return abort(404, 'Token tidak ditemukan');
        }

        // Kemudian, cari user berdasarkan token
        $user = User::where('api_token', $token)->first();
        if ($user) {
            // Jika user ditemukan, lanjutkan dengan request
            return $next($request);
        } else {
            // Jika user tidak ditemukan, kembalikan error
            return abort(404, 'Token tidak valid');
        }
    }
}
