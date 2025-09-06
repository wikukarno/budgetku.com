<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureE2EEIsSetup
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && !$user->e2ee_enabled) {
            // Allow E2EE setup and key routes to pass through
            if ($request->is('e2ee/setup') || $request->is('e2ee/*') || $request->is('api/e2ee/*')) {
                return $next($request);
            }

            // Hanya redirect untuk permintaan HTML (bukan JSON / API)
            if (!$request->expectsJson() && $request->isMethod('GET')) {
                return redirect()->to('/e2ee/setup');
            }
        }

        return $next($request);
    }
}
