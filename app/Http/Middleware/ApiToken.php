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

        $user = User::where('api_token', $token)->first();
        if ($token) {
            if ($user) {
                return $next($request);
            } else {
                return response()->json([
                    'message' => 'Token tidak valid'
                ], 401);
            }
        }
    }
}
