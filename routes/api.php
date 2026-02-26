<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\CarController;
use App\Http\Controllers\RentalController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\LineController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::apiResource('clients', ClientController::class);
    Route::apiResource('cars', CarController::class);
    Route::apiResource('rentals', RentalController::class);
    Route::apiResource('brands', BrandController::class);
    Route::apiResource('lines', LineController::class);
});
