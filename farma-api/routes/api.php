<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PenerimaanController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ObatController;
use App\Http\Controllers\Api\PenjualanController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::post('/login', [AuthController::class, 'login'])->name('login');

/*
|--------------------------------------------------------------------------
| Protected Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {

    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::post('/logout', [AuthController::class, 'logout']);

    /*
    | Role: Apoteker, Admin, Superadmin
    */
    Route::middleware('role:apoteker,admin,superadmin')->group(function () {
        // Penerimaan Obat
        Route::post('/penerimaan', [PenerimaanController::class, 'store']);
        Route::get('/penerimaan', [PenerimaanController::class, 'index']);
        
        // Suppliers
        Route::apiResource('suppliers', App\Http\Controllers\Api\SupplierController::class);

        // obat
        Route::get('/obat/export', [ObatController::class, 'export']); // Export route
        Route::get('/obat', [ObatController::class, 'index']);
        Route::post('/obat', [ObatController::class, 'store']);
        Route::get('/obat/{id}', [ObatController::class, 'show']);
        Route::put('/obat/{id}', [ObatController::class, 'update']);
        Route::delete('/obat/{id}', [ObatController::class, 'destroy']);

    // penjualan
    Route::prefix('penjualan')->group(function () {
        Route::get('/', [PenjualanController::class, 'index']);   // list penjualan
        Route::post('/', [PenjualanController::class, 'store']);  // tambah penjualan
        Route::get('/{id}', [PenjualanController::class, 'show']); // detail penjualan
    });

    // Laporan Keuangan
    Route::get('/laporan', [App\Http\Controllers\Api\LaporanController::class, 'index']);
    Route::get('/laporan/export-pdf', [App\Http\Controllers\Api\LaporanController::class, 'exportPdf']);

    /*
    | Role: Superadmin
    */
    Route::middleware('role:superadmin')->group(function () {
        Route::get('/users', [UserController::class, 'index']);
        Route::post('/users', [UserController::class, 'store']);
        Route::put('/users/{id}', [UserController::class, 'update']); // Tambah ini
        Route::delete('/users/{id}', [UserController::class, 'destroy']);
    });
    });
});