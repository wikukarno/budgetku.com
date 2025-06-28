<?php
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\TwoFactorController;
use Illuminate\Support\Facades\Auth;
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

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/terms-and-conditions', [HomeController::class, 'termsAndConditions'])->name('terms');

Route::get('/auth/callback', [LoginController::class, 'handlerProviderCallback']);
Route::get('/auth/redirect', [LoginController::class, 'redirectToProvider']);

Auth::routes(['register' => false, 'reset' => false, 'verify' => false]);

Route::get('/2fa/prompt', [TwoFactorController::class, 'prompt'])->name('2fa.prompt');
Route::post('/2fa/verify/login', [TwoFactorController::class, 'verifyLogin'])->name('2fa.verify.login');

Route::middleware(['auth'])->group(function () {
    Route::post('/2fa/setup', [TwoFactorController::class, 'setup'])->name('2fa.setup');
    Route::post('/2fa/verify', [TwoFactorController::class, 'verify'])->name('2fa.verify');
    Route::post('/2fa/mark-downloaded', [TwoFactorController::class, 'markRecoveryDownloaded'])->name('2fa.mark.downloaded');
    Route::post('/2fa/disable', [TwoFactorController::class, 'disable'])->name('2fa.disable');
});