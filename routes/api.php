<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\OrderController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\ItemController;
use App\Http\Controllers\API\UserController;

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::prefix('/auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/me', [AuthController::class, 'me'])->middleware('auth:sanctum');
    Route::get('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
});

Route::middleware(['auth:sanctum'])->group(function () {
    //user
    Route::post('/user', [UserController::class, 'store'])->middleware(['createUser']);

    //item
    Route::get('/item', [ItemController::class, 'index']);
    Route::post('/item', [ItemController::class, 'store'])->middleware(['createUpdateItem']);
    Route::post('/item/{id}', [ItemController::class, 'update'])->middleware(['createUpdateItem']);

    //order
    Route::get('/order/{id}/on-progress-order', [OrderController::class, 'onProgressOrder'])->middleware(['onProgressOrder']);
    Route::get('/order/{id}/finish-order', [OrderController::class, 'finishOrder'])->middleware(['finishOrder']);
    Route::get('/order/{id}/pay-order', [OrderController::class, 'payOrder'])->middleware(['updatePayOrder']);
    Route::get('/order', [OrderController::class, 'index']);
    Route::get('/order/{id}', [OrderController::class, 'show']);
    Route::post('/order', [OrderController::class, 'store'])->middleware(['createOrder']);
});


