<?php

use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\StoreController;
use App\Http\Controllers\API\StoreItemController;
use App\Http\Controllers\API\UserController;
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

Route::middleware('auth:sanctum')->group(function () {

    Route::post('update-profile', [UserController::class, 'updateProfile']);

    Route::post('save-store', [StoreController::class, 'store']);
    Route::post('update-store', [StoreController::class, 'update']);
    Route::post('update-product', [ProductController::class, 'update']);

    Route::post('create-product', [ProductController::class, 'store']);
    Route::post('create-product-item', [StoreItemController::class, 'create']);

});

Route::post('login', [UserController::class, 'login']);
Route::post('register', [UserController::class, 'register']);

Route::get('all-stores', [StoreController::class, 'all']);
Route::get('all-products', [ProductController::class, 'all']);
Route::get('store-items', [StoreItemController::class, 'productList']);
Route::get('users', [UserController::class, 'profile']);
