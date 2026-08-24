<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\FinancialReportController;
use App\Http\Controllers\Api\ResidentController;
use App\Http\Controllers\Api\GalleryController;
use App\Http\Controllers\Api\OriginCampusController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\RoomNumberController;
use App\Http\Controllers\Api\OriginCityController;
use App\Http\Controllers\Api\StaticController;
use App\Http\Controllers\Api\PendaftaranController;
use App\Http\Controllers\Api\UserController;
use App\Http\Middleware\HeaderMiddleware;
use App\Http\Middleware\JwtMiddleware;
use Illuminate\Support\Facades\Route;

// ==========================================
// 1. GRUP AUTHENTICATION (Login, Register, Token)
// Endpoint: /api/v1/auth/...
// ==========================================
Route::group([
    'prefix' => 'v1/auth',
    'middleware' => [HeaderMiddleware::class],
], function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('check-token', [AuthController::class, 'checkToken']);
    Route::post('refresh-token', [AuthController::class, 'refreshToken']);
});


// ==========================================
// 2. GRUP PUBLIC (Bisa diakses tanpa login)
// Endpoint: /api/v1/public/...
// ==========================================
Route::middleware([HeaderMiddleware::class])->group(function () {
    Route::prefix('v1/public')->group(function () {
        Route::get('categories', [CategoryController::class, 'index']);
        Route::get('galleries', [GalleryController::class, 'index']); // ✅ SUDAH ADA
        Route::post('pendaftaran', [PendaftaranController::class, 'store']);
    });
});


// ==========================================
// 3. GRUP PROTECTED (Wajib Login / Token JWT)
// Endpoint: /api/v1/...
// ==========================================
Route::middleware([JwtMiddleware::class, HeaderMiddleware::class])->group(function () {
    Route::prefix('v1')->group(function () {

        // Auth
        Route::post('/logout', [AuthController::class, 'logout']);

        // 🔥 USER PROFILE
        Route::get('/user/profile', [UserController::class, 'profile']);
        Route::get('/me', [UserController::class, 'profile']);
        Route::get('/user', [UserController::class, 'profile']);

        // ==========================================
        // 🔥 RESIDENTS (DARI PENDAFTARAN) - BARU
        // ==========================================
        Route::get('residents/user/{userId}', [ResidentController::class, 'getByUserId']);
        Route::post('residents/from-pendaftaran', [ResidentController::class, 'storeFromPendaftaran']);
        Route::put('residents/{id}/from-pendaftaran', [ResidentController::class, 'updateFromPendaftaran']);

        // ==========================================
        // RESIDENTS (SUDAH ADA)
        // ==========================================
        Route::get('residents', [ResidentController::class, 'index']);
        Route::get('get-residents-index', [ResidentController::class, 'getIndex']);
        Route::post('residents', [ResidentController::class, 'store']);
        Route::get('residents/{id}', [ResidentController::class, 'show']);
        Route::put('residents/{id}', [ResidentController::class, 'update']);
        Route::delete('residents/{id}', [ResidentController::class, 'destroy']);

        // ==========================================
        // PENDAFTARAN
        // ==========================================
        Route::get('pendaftaran', [PendaftaranController::class, 'index']); 
        Route::put('pendaftaran/{id}', [PendaftaranController::class, 'updateStatus']);

        // ==========================================
        // MASTER DATA
        // ==========================================
        Route::get('room-numbers', [RoomNumberController::class, 'index']);
        Route::get('origin-campuses', [OriginCampusController::class, 'index']);
        Route::get('origin-cities', [OriginCityController::class, 'index']);

        // ==========================================
        // GALLERIES
        // ==========================================
        // ✅ TAMBAHKAN ROUTE GET UNTUK GALLERIES
        Route::get('galleries', [GalleryController::class, 'index']); // ← DITAMBAHKAN
        Route::post('galleries', [GalleryController::class, 'store']);
        Route::get('galleries/{id}', [GalleryController::class, 'show']);
        Route::get('galleries/get-file/{id}', [GalleryController::class, 'showFile']);
        Route::put('galleries/{id}', [GalleryController::class, 'update']);
        Route::delete('galleries/{id}', [GalleryController::class, 'destroy']);

        // ==========================================
        // PAYMENTS
        // ==========================================
        Route::get('payments', [PaymentController::class, 'index']);
        Route::post('payments', [PaymentController::class, 'store']);
        Route::get('payments/{id}', [PaymentController::class, 'show']);
        Route::get('payments/get-file/{id}', [PaymentController::class, 'showFile']);
        Route::put('payments/{id}', [PaymentController::class, 'update']);
        Route::delete('payments/{id}', [PaymentController::class, 'destroy']);

        // ==========================================
        // REPORTS
        // ==========================================
        Route::get('reports', [FinancialReportController::class, 'index']);
        Route::post('reports', [FinancialReportController::class, 'store']);
        Route::post('reports/export', [FinancialReportController::class, 'exportReport']);
        Route::get('reports/{id}', [FinancialReportController::class, 'show']);
        Route::get('reports/get-file/{id}', [FinancialReportController::class, 'showFile']);
        Route::put('reports/{id}', [FinancialReportController::class, 'update']);
        Route::put('reports-sync', [FinancialReportController::class, 'syncPayment']);
        Route::delete('reports/{id}', [FinancialReportController::class, 'destroy']);
        Route::get('reports/generate/get-file/{filename}', [FinancialReportController::class, 'showFileReport']);

        // ==========================================
        // GRAFIK / STATISTIK
        // ==========================================
        Route::get('residents/grafik/active', [StaticController::class, 'getResidentActive']);
        Route::get('rooms/grafik/occupied', [StaticController::class, 'getOccupiedRoom']);
        Route::get('payments/grafik/sync', [StaticController::class, 'getSinkronisasiPayment']);
        Route::get('income/grafik/{bulan}', [StaticController::class, 'getPemasukan']);
        Route::get('outcome/grafik/{bulan}', [StaticController::class, 'getPengeluran']);
    });
});