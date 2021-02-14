<?php

use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\AuthController;
use \App\Http\Controllers\RegisterController;
use \App\Http\Controllers\ProductController;

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



Route::group([
    'prefix' => 'auth'
], function ($router) {

    Route::post('register', [RegisterController::class, 'registerUser']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
});



//Product Routes

Route::get('products', [ProductController::class, 'index']);
Route::put('update-product/{product}', [ProductController::class, 'update']);
Route::get('search/{Id}', [ProductController::class, 'show']);
Route::post('add-product', [ProductController::class, 'store']);
Route::post('checkout/', [ProductController::class, 'checkout']);
Route::post('delete-product/{Id}', [ProductController::class, 'destroy']);
