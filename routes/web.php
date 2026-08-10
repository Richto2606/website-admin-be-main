<?php

use Illuminate\Support\Facades\Route;

// ==========================================
// ROUTE UTAMA - TAMPILAN WELCOME
// ==========================================
Route::get('/', function () {
    return view('welcome');
});

// ==========================================
// 🔥 ROUTE LOGIN - REDIRECT KE NEXT.JS
// ==========================================
Route::get('/login', function () {
    return redirect('http://localhost:3000/login');
})->name('login');

// ==========================================
// ROUTE YANG MEMERLUKAN AUTHENTIKASI
// ==========================================
Route::middleware(['auth'])->group(function () {

    // Group khusus untuk role: Admin
    Route::middleware(['role:Admin'])->prefix('admin')->group(function () {
        Route::get('/dashboard', function () {
            return 'Halaman Dashboard Admin';
        })->name('admin.dashboard');
    });

    // Group khusus untuk role: Member
    Route::middleware(['role:Member'])->prefix('user')->group(function () {
        Route::get('/dashboard', function () {
            return 'Halaman Dashboard Member';
        })->name('user.dashboard');
    });

});