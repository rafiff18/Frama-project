<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Superadmin\CafeController;
use App\Http\Controllers\Superadmin\UserController;
use App\Http\Controllers\Auth\AuthController;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware(['auth:sanctum','role:superadmin'])->group(function () {
    Route::apiResource('cafes', CafeController::class)->only(['index','store']);
    Route::post('users', [UserController::class,'store']);
});
