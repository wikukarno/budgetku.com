<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\E2EEKeyController;

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
    Route::get('/e2ee/keys', [E2EEKeyController::class, 'show']);
    Route::post('/e2ee/keys', [E2EEKeyController::class, 'store']);
    Route::post('/e2ee/passphrase/rotate', [E2EEKeyController::class, 'rotatePassphrase']);
    Route::post('/e2ee/recovery/rotate', [E2EEKeyController::class, 'rotateRecovery']);
    Route::post('/e2ee/keypair/rotate', [E2EEKeyController::class, 'rotateKeypair']);
    Route::get('/e2ee/keypair/{version}', [E2EEKeyController::class, 'getKeypair']);
});
