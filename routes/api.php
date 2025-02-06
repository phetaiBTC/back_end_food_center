<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderItemController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VendorsController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::get('/email/verify/{token}', [AuthController::class, 'verify'])->name('verification.verify');

// Route::middleware(['jwt.auth'])->group(function () {
    Route::get('/auth/me', [AuthController::class, 'me']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::post('/auth/tokenDetails', [AuthController::class, 'tokenDetails']);

    Route::prefix('user')->name('user.')->group(function () {
        Route::get('/getall', [UserController::class, 'getAll'])->name('getall');
        Route::get('/{id}', [UserController::class, 'getOne'])->name('getone');
        Route::put('/{id}', [UserController::class, 'update'])->name('update');
        Route::delete('/{id}', [UserController::class, 'delete'])->name('delete');
    });
    Route::prefix('vendor')->name('vendor.')->group(function () {
        Route::get('/getall', [VendorsController::class, 'getAll'])->name('getall');
        Route::get('/{id}', [VendorsController::class, 'getOne'])->name('getone');
        Route::post('/update/{id}', [VendorsController::class, 'update'])->name('update');
        Route::delete('/{id}', [VendorsController::class, 'delete'])->name('delete');
        Route::post('/create', [VendorsController::class, 'create'])->name('create');
    });
    Route::prefix('menu')->name('menu.')->group(function () {
        Route::get('/getall', [MenuController::class, 'getAll'])->name('getall');
        Route::get('/{id}', [MenuController::class, 'getOne'])->name('getone');
        Route::post('/create', [MenuController::class, 'create'])->name('create');
        Route::post('/update/{id}', [MenuController::class, 'update'])->name('update');
        Route::delete('/{id}', [MenuController::class, 'delete'])->name('delete');
    });
    Route::prefix('order-item')->name('orderitem.')->group(function () {
        Route::post('/create', [OrderItemController::class, 'create'])->name('create');
        Route::get('/{order_id}', [OrderItemController::class, 'getOrderItems'])->name('getOrderItems');
        Route::put('/{id}', [OrderItemController::class, 'update'])->name('update');
        Route::delete('/{id}', [OrderItemController::class, 'delete'])->name('delete');
    });
    Route::prefix('order')->name('order.')->group(function () {
        Route::post('/create', [OrderController::class, 'create'])->name('create');
        Route::get('/{id}', [OrderController::class, 'getOne'])->name('getone');
        Route::put('/{id}', [OrderController::class, 'update'])->name('update');
        Route::delete('/{id}', [OrderController::class, 'delete'])->name('delete');
    });
    Route::prefix('category')->name('category.')->group(function () {
        Route::get('/getall', [CategoriesController::class, 'getall'])->name('getall');
        Route::get('/{id}', [CategoriesController::class, 'getOne'])->name('getone');
        Route::post('/create', [CategoriesController::class, 'create'])->name('create');
        Route::post('/update/{id}', [CategoriesController::class, 'update'])->name('update');
        Route::delete('/{id}', [CategoriesController::class, 'delete'])->name('delete');
    });
// });
