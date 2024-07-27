<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/user', [AuthController::class, 'register']);
Route::post('/user/login', [AuthController::class, 'login']);

Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{id}', [ProductController::class, 'get'])->where('id', '[0-9]+');

Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{id}', [CategoryController::class, 'get'])->where('id', '[0-9]+');
Route::get('/categories/{id}/products', [CategoryController::class, 'products'])->where('id', '[0-9]+');


Route::middleware('auth:sanctum')->group(function () {
    Route::delete('/user', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    // product
    Route::post('/products', [ProductController::class, 'store'])->middleware('ability:admin');
    Route::put('/products/{id}', [ProductController::class, 'update'])->where('id', '[0-9]+')->middleware('ability:admin');
    Route::delete('/products/{id}', [ProductController::class, 'delete'])->where('id', '[0-9]+')->middleware('ability:admin');

    // category
    Route::post('/categories', [CategoryController::class, 'store'])->middleware('ability:admin');
    Route::put('/categories/{id}', [CategoryController::class, 'update'])->where('id', '[0-9]+')->middleware('ability:admin');
    Route::delete('/categories/{id}', [CategoryController::class, 'delete'])->where('id', '[0-9]+')->middleware('ability:admin');

    // cart
    Route::post('/cart', [CartController::class, 'add']);
    Route::post('/cart/reduce', [CartController::class, 'reduce']);
    Route::get('/cart', [CartController::class, 'show']);
    Route::delete('/cart', [CartController::class, 'remove']);

    // order
    Route::post('/order', [OrderController::class, 'store']);
    Route::get('/order', [OrderController::class, 'show']);
});
