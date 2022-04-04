<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CoinController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ServiceController;

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

Route::get("/coin", [CoinController::class, 'list']);
Route::post("/coin", [CoinController::class, 'insert']);
Route::delete("/coin", [CoinController::class, 'returnCoins']);

Route::post("/order", [OrderController::class, 'createOrder']);

Route::post("/service", [ServiceController::class, 'newService']);

