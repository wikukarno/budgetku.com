<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

class E2EESessionController extends Controller
{
    protected function ttlMinutes(): int
    {
        return (int) (env('E2EE_SESSION_TTL_MIN', 60 * 24 * 30)); // default 30 hari
    }

    protected function cookieSecure(): bool
    {
        return app()->environment('production');
    }

    protected function cookieDomain(): ?string
    {
        $domain = config('session.domain');
        return $domain ?: null;
    }

    protected function cookieSameSite(): string
    {
        $same = config('session.same_site', 'lax');
        return in_array(strtolower((string) $same), ['lax','strict','none']) ? strtolower($same) : 'lax';
    }

    protected function ensureSessionKey(Request $request): array
    {
        $now = Carbon::now();
        $ttl = $this->ttlMinutes();
        $sess = $request->session()->get('e2ee_session_key');

        if (!$sess || empty($sess['key']) || empty($sess['exp']) || $now->greaterThan(Carbon::createFromTimestamp($sess['exp']))) {
            $key = base64_encode(random_bytes(32));
            $exp = $now->copy()->addMinutes($ttl)->timestamp; // detik unix timestamp
            $sess = [
                'id' => (string) Str::uuid(),
                'key' => $key,
                'exp' => $exp,
            ];
            $request->session()->put('e2ee_session_key', $sess);
        }

        return $sess;
    }

    public function getKey(Request $request)
    {
        $sess = $this->ensureSessionKey($request);
        return response()->json([
            'key' => $sess['key'],     // base64
            'exp' => $sess['exp'],     // unix timestamp (seconds)
            'key_id' => $sess['id'],
            'ttl_min' => $this->ttlMinutes(),
        ]);
    }

    public function refreshKey(Request $request)
    {
        $request->session()->forget('e2ee_session_key');
        $sess = $this->ensureSessionKey($request);
        return response()->json([
            'key' => $sess['key'],
            'exp' => $sess['exp'],
            'key_id' => $sess['id'],
            'ttl_min' => $this->ttlMinutes(),
        ]);
    }

    public function setWrap(Request $request)
    {
        $request->validate([
            'wrap' => ['required','string'], // base64(iv|ciphertext) URL-encoded
            'persist' => ['nullable','boolean'],
        ]);

        $ttl = $this->ttlMinutes();
        $persist = (bool) $request->boolean('persist', false);
        // Jika persist, gunakan TTL panjang (hari → menit)
        $persistMinutes = (int) (env('E2EE_WRAP_PERSIST_DAYS', 180) * 24 * 60);
        $minutes = $persist ? $persistMinutes : $ttl;
        // Cookie nama tetap: bk_wr (BudgetKu Wrapped R)
        $cookie = cookie(
            name: 'bk_wr',
            value: $request->string('wrap'),
            minutes: $minutes,
            path: '/',
            domain: $this->cookieDomain(),
            secure: $this->cookieSecure(),
            httpOnly: false,
            raw: false,
            sameSite: $this->cookieSameSite()
        );

        // Set flag agar logout tidak menghapus jika persist=true
        $keep = null;
        if ($persist) {
            $keep = cookie(
                name: 'bk_wr_keep',
                value: '1',
                minutes: $persistMinutes,
                path: '/',
                domain: $this->cookieDomain(),
                secure: $this->cookieSecure(),
                httpOnly: false,
                raw: false,
                sameSite: $this->cookieSameSite()
            );
        }

        $resp = response()->json(['status' => true]);
        $resp = $resp->withCookie($cookie);
        if ($keep) $resp = $resp->withCookie($keep);
        return $resp;
    }

    public function clearWrap(Request $request)
    {
        // Set cookie dengan waktu negatif agar dihapus, atribut harus konsisten
        $cookie = cookie(
            name: 'bk_wr',
            value: '',
            minutes: -60,
            path: '/',
            domain: $this->cookieDomain(),
            secure: $this->cookieSecure(),
            httpOnly: false,
            raw: false,
            sameSite: $this->cookieSameSite()
        );
        $keep = cookie(
            name: 'bk_wr_keep',
            value: '',
            minutes: -60,
            path: '/',
            domain: $this->cookieDomain(),
            secure: $this->cookieSecure(),
            httpOnly: false,
            raw: false,
            sameSite: $this->cookieSameSite()
        );
        return response()->json(['status' => true])->withCookie($cookie)->withCookie($keep);
    }
}
