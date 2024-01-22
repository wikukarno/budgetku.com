<?php

use App\Http\Controllers\API\AboutsController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\DocumentController;
use App\Http\Controllers\API\FinancesController;
use App\Http\Controllers\API\PortofoliosController;
use App\Http\Controllers\API\ProxyController;
use App\Http\Controllers\API\SalaryController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->group(function () {
    Route::get('salary', [SalaryController::class, 'fetch']);

    Route::get('user', [AuthController::class, 'fetch']);
    Route::post('logout', [AuthController::class, 'logout']);
});

// make route with token
Route::middleware('access-token')
    ->group(function () {
        Route::get('portofolios', [PortofoliosController::class, 'all']);
        Route::get('abouts', [AboutsController::class, 'all']);
        Route::get('documents', [DocumentController::class, 'all']);
        Route::post('login', [AuthController::class, 'login'])->middleware('throttle:3,1');
    });
