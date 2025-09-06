<?php
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\TwoFactorController;
use App\Http\Controllers\API\E2EEKeyController;
use App\Http\Controllers\API\E2EESessionController;
use App\Http\Controllers\Auth\RefreshAuthController;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function(){ return Inertia::render('Landing'); })->name('home');
Route::get('/', [HomeController::class, 'index'])->name('home');
// Policy pages via Inertia
Route::get('/terms-and-conditions', function(){ return Inertia::render('Policies/Terms'); })->name('terms');
Route::get('/privacy-policy', function(){ return Inertia::render('Policies/Privacy'); })->name('privacy.policy');
Route::get('/cookie-policy', function(){ return Inertia::render('Policies/Cookie'); })->name('cookie.policy');
Route::get('/data-protection', function(){ return Inertia::render('Policies/DataProtection'); })->name('data.protection');

Route::get('/auth/callback', [LoginController::class, 'handlerProviderCallback']);
Route::get('/auth/redirect', [LoginController::class, 'redirectToProvider']);

// Enable custom auth routes
// Auth routes yang menggunakan Inertia
Route::get('register', function () { return Inertia::render('Auth/Register'); })->name('register');
Route::post('register', [RegisterController::class, 'register']);

Route::get('login', function () { return Inertia::render('Auth/Login'); })->name('login');

// Override default auth methods untuk menggunakan Inertia
Route::post('login', function (\Illuminate\Http\Request $request) {
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    $credentials = $request->only('email', 'password');
    $remember = $request->boolean('remember');

    if (Auth::attempt($credentials, $remember)) {
        $request->session()->regenerate();

        $user = Auth::user();
        
        // Send login notification
        try {
            $user->notify(new \App\Notifications\UserLoginNotification());
        } catch (\Exception $e) {
            // Notification failed, but don't block login
        }

        $redirect = $user->roles === 'Owner' 
            ? route('admin.dashboard') 
            : route('customer.dashboard');

        // If this is an AJAX/JSON request (e.g., SPA login), return JSON so client can finish E2EE wrap before navigating
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'status' => true,
                'redirect' => $redirect,
            ]);
        }

        return Inertia::location($redirect);
    }

    if ($request->expectsJson() || $request->ajax()) {
        return response()->json([
            'status' => false,
            'message' => 'The provided credentials do not match our records.',
        ], 422);
    }

    return back()->withErrors([
        'email' => 'The provided credentials do not match our records.',
    ]);
})->name('login.post');

Route::post('logout', function (\Illuminate\Http\Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    $secure = app()->environment('production');
    $domain = config('session.domain') ?: null;
    $sameSite = config('session.same_site', 'lax');

    // Jika user memilih persist (bk_wr_keep=1), jangan hapus bk_wr saat logout
    $keep = (string) request()->cookie('bk_wr_keep', '') === '1';
    $resp = Inertia::location('/');
    if (!$keep) {
        $forgetWrap = cookie('bk_wr', '', -60, '/', $domain, $secure, false, false, $sameSite);
        $resp = $resp->withCookie($forgetWrap);
    }
    // Selalu hapus flag keep pada logout
    $forgetKeep = cookie('bk_wr_keep', '', -60, '/', $domain, $secure, false, false, $sameSite);
    return $resp->withCookie($forgetKeep);
})->name('logout');

Route::get('/2fa/prompt', [TwoFactorController::class, 'prompt'])->name('2fa.prompt');
Route::post('/2fa/verify/login', [TwoFactorController::class, 'verifyLogin'])->name('2fa.verify.login');

Route::middleware(['auth'])->group(function () {
    Route::post('/2fa/setup', [TwoFactorController::class, 'setup'])->name('2fa.setup');
    Route::post('/2fa/verify', [TwoFactorController::class, 'verify'])->name('2fa.verify');
    Route::post('/2fa/mark-downloaded', [TwoFactorController::class, 'markRecoveryDownloaded'])->name('2fa.mark.downloaded');
    Route::post('/2fa/disable', [TwoFactorController::class, 'disable'])->name('2fa.disable');

    // E2EE Setup via Inertia
    Route::get('/e2ee/setup', function() { return Inertia::render('E2EE/Setup'); })->name('e2ee.setup');
    Route::view('/e2ee/recover', 'e2ee.recover')->name('e2ee.recover');
    Route::get('/e2ee/keys', [E2EEKeyController::class, 'show'])->name('e2ee.keys.show');
    Route::post('/e2ee/keys', [E2EEKeyController::class, 'store'])->name('e2ee.keys.store');
    Route::post('/e2ee/passphrase/rotate', [E2EEKeyController::class, 'rotatePassphrase'])->name('e2ee.keys.rotate');
    Route::post('/e2ee/recovery/rotate', [E2EEKeyController::class, 'rotateRecovery'])->name('e2ee.recovery.rotate');
    Route::post('/e2ee/keypair/rotate', [E2EEKeyController::class, 'rotateKeypair'])->name('e2ee.keypair.rotate');
    Route::get('/e2ee/keypair/{version}', [E2EEKeyController::class, 'getKeypair'])->name('e2ee.keypair.get');
    Route::post('/e2ee/account-wrap', [E2EEKeyController::class, 'setAccountWrap'])->name('e2ee.account.wrap');

    // E2EE Session Key (memory-only client, wrap stored in cookie)
    Route::get('/e2ee/session/key', [E2EESessionController::class, 'getKey'])->name('e2ee.session.key');
    Route::post('/e2ee/session/key/refresh', [E2EESessionController::class, 'refreshKey'])->name('e2ee.session.refresh');
    Route::post('/e2ee/session/wrap', [E2EESessionController::class, 'setWrap'])->name('e2ee.session.wrap');
    Route::post('/e2ee/session/clear', [E2EESessionController::class, 'clearWrap'])->name('e2ee.session.clear');
    // Issue refresh cookie explicitly (optional)
    Route::post('/auth/refresh/issue', [RefreshAuthController::class, 'issue'])->name('auth.refresh.issue');
});

// Silent auth refresh (works even if session expired, protected by HttpOnly cookie)
Route::post('/auth/refresh', [RefreshAuthController::class, 'refresh'])->name('auth.refresh');
