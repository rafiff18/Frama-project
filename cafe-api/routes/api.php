<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\OrderController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::post('/login', [AuthController::class, 'login']);

/*
|--------------------------------------------------------------------------
| Protected Routes (Perlu Login)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {

    // Identitas User & Logout
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::post('/logout', [AuthController::class, 'logout']);

    // Menu: Akses umum untuk semua Role yang sudah login
    Route::get('/menus', [MenuController::class, 'index']);
    Route::get('/menus/{id}', [MenuController::class, 'show']);

    /*
    |--- Role: Admin & Superadmin (Manajemen Menu) ---
    */
    Route::middleware('role:admin,superadmin')->group(function () {
        Route::post('/menus', [MenuController::class, 'store']);
        Route::post('/menus/{id}', [MenuController::class, 'update']); // Handling multipart/form-data
        Route::delete('/menus/{id}', [MenuController::class, 'destroy']);
    });

    /*
    |--- Role: Kasir, Admin, Superadmin (Transaksi) ---
    */
    Route::middleware('role:kasir,admin,superadmin')->group(function () {
        Route::post('/orders', [OrderController::class, 'store']); // Membuat pesanan
    });

    /*
    |--- Role: Chef, Admin, Superadmin (Operasional Dapur) ---
    */
    Route::middleware('role:chef,admin,superadmin')->group(function () {
        Route::patch('/orders/{id}/status', [OrderController::class, 'updateStatus']); // Update status masak
    });

    /*
    |--- Role: Owner, Admin, Superadmin (Monitoring) ---
    */
    Route::middleware('role:owner,admin,superadmin')->group(function () {
        Route::get('/orders', [OrderController::class, 'index']); // Melihat semua transaksi
    });
    Route::middleware('role:kasir,admin,superadmin')->group(function () {
        Route::post('/orders/{id}/checkout', [OrderController::class, 'checkout']);
    });
});