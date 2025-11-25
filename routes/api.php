<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\SupplierController;

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

Route::post('/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth routes
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    Route::get('/tokens', [AuthController::class, 'tokens']);

    // Resource routes
    Route::apiResource('/barang', BarangController::class);
    Route::apiResource('/supplier', SupplierController::class);
    Route::apiResource('/kategori', KategoriController::class);

    // Additional routes
    Route::post('/barang/{id}/restore', [BarangController::class, 'restore']);
    Route::get('/barang/low-stock', [BarangController::class, 'lowStock']);
});
