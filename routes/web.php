<?php

use App\Http\Controllers\Admin\AboutController;
use App\Http\Controllers\Admin\AccountController;
use App\Http\Controllers\Admin\CategoryFinanceController;
use App\Http\Controllers\Admin\DashboardAdminController;
use App\Http\Controllers\Admin\DocumentController;
use App\Http\Controllers\Admin\FinanceController;
use App\Http\Controllers\Admin\PortofoliosController;
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

Route::get('/', function () {
    return view('auth.login');
});

Route::prefix('/pages/dashboard')
    ->middleware(['auth', 'owner'])
    ->group(function () {
        Route::get('/', [DashboardAdminController::class, 'index'])->name('dashboard');
        Route::post('/ubah-profile', [AccountController::class, 'ubahProfile'])->name('ubah-profile');
        Route::post('/show/kategori', [CategoryFinanceController::class, 'show']);
        Route::post('/delete/kategori', [CategoryFinanceController::class, 'destroy']);

        Route::resource('about', AboutController::class);
        Route::resource('portofolio', PortofoliosController::class);
        Route::resource('document', DocumentController::class);
        Route::resource('finance', FinanceController::class);
        Route::resource('category-finance', CategoryFinanceController::class);
        Route::resource('account', AccountController::class);
    });


Auth::routes(['register' => false]);
