<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\V1\ServicreditoController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::prefix('v1/Servicredito')->group(function() {
    Route::controller(ServicreditoController::class)->group(function () {
        Route::get('/getClientInfo/{id}', 'getClientInfo');
        Route::get('/getCreditsInfo/{id}', 'getCreditsInfo');
        Route::get('/downloadPaymentPlan/{id}', 'downloadPaymentPlan');
        Route::post('/getCreditDetail', 'getCreditDetail');
        Route::post('/loginClient', 'loginClient');
        Route::post('/passwordReset', 'passwordReset');
        Route::post('/getLastPayment', 'getLastPayment');
        Route::post('/generateCertificate', 'generateCertificate');
    });
});
