<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTwoFactorVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        // Skip if not login or 2FA tidak diaktifkan
        if (!$user || !$user->two_factor_enabled) {
            return $next($request);
        }

        // Skip route 2FA prompt & verify biar gak loop
        if ($request->routeIs('2fa.prompt') || $request->routeIs('2fa.verify.login')) {
            return $next($request);
        }

        // Kalau belum verifikasi OTP
        if (!$request->session()->get('2fa_verified')) {
            return redirect()->route('2fa.prompt');
        }

        return $next($request);
    }
}
