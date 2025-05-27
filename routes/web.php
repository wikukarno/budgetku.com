<?php
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\HomeController;
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

Route::middleware(['web'])->group(function () {
    Route::get('/auth/callback', [LoginController::class, 'handlerProviderCallback']);
    Route::get('/auth/redirect', [LoginController::class, 'redirectToProvider']);
});


Auth::routes(['register' => false, 'reset' => false, 'verify' => false]);
