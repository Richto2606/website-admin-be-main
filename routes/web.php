<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Semua route di bawah ini mewajibkan user untuk login (auth)
Route::middleware(['auth'])->group(function () {

    // Group khusus untuk role: Admin
    Route::middleware(['role:Admin'])->prefix('admin')->group(function () {
        Route::get('/dashboard', function () {
            return 'Halaman Dashboard Admin';
        })->name('admin.dashboard');

        // Tambahkan route admin lainnya di sini
    });

    // Group khusus untuk role: Member
    Route::middleware(['role:Member'])->prefix('user')->group(function () {
        Route::get('/dashboard', function () {
            return 'Halaman Dashboard Member';
        })->name('user.dashboard');

        // Tambahkan route member lainnya di sini
    });

});
