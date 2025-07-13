<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\OrderController;
use Illuminate\Http\Request;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
    Route::get('/orders/dashboard', [OrderController::class, 'dashboard']);

 Route::middleware('jwt.auth')->group(function () {
    Route::get('/customers/', [CustomerController::class, 'index']);
    Route::post('/customers/', [CustomerController::class, 'store']);
    Route::put('/customers/{id}', [CustomerController::class, 'update']);
    Route::delete('/customers/{id}', [CustomerController::class, 'destroy']);



    Route::get('/orders/', [OrderController::class, 'index']);
    Route::get('/orders/getTotalOrders', [OrderController::class, 'getTotalOrders']);
    Route::get('/orders/statusSummary', [OrderController::class, 'statusSummary']);
    Route::get('/orders/groupedCount', [OrderController::class, 'groupedCount']);
    Route::get('/orders/getFilters', [OrderController::class, 'getFilters']);

    Route::post('/orders/', [OrderController::class, 'store']);
    Route::put('/orders/{id}', [OrderController::class, 'update']);
    Route::put('/orders/updateStatus/{id}', [OrderController::class, 'updateStatus']);

    Route::delete('/orders/{id}', [OrderController::class, 'destroy']);
});



