<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserRefreshToken;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class RefreshAuthController extends Controller
{
    protected function cookieSecure(): bool { return app()->environment('production'); }
    protected function cookieDomain(): ?string { return config('session.domain') ?: null; }
    protected function cookieSameSite(): string { return config('session.same_site', 'lax'); }
    protected function persistDays(): int { return (int) (env('AUTH_REFRESH_DAYS', 365)); }

    protected function issueRefreshCookie(User $user, Request $request)
    {
        $token = Str::random(64);
        $hash = hash('sha256', $token);
        $expires = Carbon::now()->addDays($this->persistDays());

        UserRefreshToken::create([
            'users_uuid' => $user->uuid,
            'token_hash' => $hash,
            'expires_at' => $expires,
            'user_agent' => substr((string) $request->userAgent(), 0, 255),
            'ip_address' => (string) $request->ip(),
        ]);

        return cookie(
            name: 'bk_rt',
            value: $token,
            minutes: $this->persistDays() * 24 * 60,
            path: '/',
            domain: $this->cookieDomain(),
            secure: $this->cookieSecure(),
            httpOnly: true,
            raw: false,
            sameSite: $this->cookieSameSite()
        );
    }

    public function refresh(Request $request)
    {
        if (Auth::check()) {
            return response()->json(['status' => true, 'already' => true]);
        }

        $token = (string) $request->cookie('bk_rt', '');
        if (!$token) {
            return response()->json(['status' => false], 204);
        }
        $hash = hash('sha256', $token);
        $rec = UserRefreshToken::where('token_hash', $hash)->first();
        if (!$rec || $rec->revoked_at || Carbon::now()->greaterThan($rec->expires_at)) {
            return response()->json(['status' => false], 401);
        }

        $user = User::where('uuid', $rec->users_uuid)->first();
        if (!$user) return response()->json(['status' => false], 401);

        Auth::login($user);
        $request->session()->regenerate();

        // Rotate refresh token
        $rec->revoked_at = Carbon::now();
        $rec->save();
        $cookie = $this->issueRefreshCookie($user, $request);

        return response()->json(['status' => true])->withCookie($cookie);
    }

    public function issue(Request $request)
    {
        $user = $request->user();
        if (!$user) return response()->json(['status' => false], 401);
        $cookie = $this->issueRefreshCookie($user, $request);
        return response()->json(['status' => true])->withCookie($cookie);
    }

    public function revoke(Request $request)
    {
        $token = (string) $request->cookie('bk_rt', '');
        if ($token) {
            $hash = hash('sha256', $token);
            UserRefreshToken::where('token_hash', $hash)->update(['revoked_at' => Carbon::now()]);
        }
        $forget = cookie('bk_rt', '', -60, '/', $this->cookieDomain(), $this->cookieSecure(), true, false, $this->cookieSameSite());
        return response()->json(['status' => true])->withCookie($forget);
    }
}

