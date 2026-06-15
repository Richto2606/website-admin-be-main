<?php

use Illuminate\Support\Facades\Route;
// Import Controller Login Anda di sini, misalnya:
// use App\Http\Controllers\Auth\LoginController; 

Route::get('/', function () {
    return view('welcome');
});

// --- TAMBAHKAN ROUTE LOGIN DI SINI (Di luar middleware auth) ---
// 1. Rute untuk menampilkan halaman/form login
Route::get('/login', function () {
    return view('auth.login'); // sesuaikan dengan nama file blade login Anda
})->name('login');

// 2. Rute untuk memproses submit form login (Menggunakan POST)
Route::post('/login', function (\Illuminate\Http\Request $request) {
    // Sementara untuk tes apakah rute POST sudah bekerja dan tidak error 405 lagi
    return response()->json(['message' => 'Rute POST Login berhasil diakses!', 'data' => $request->all()]);
    
    // Nanti di sini Anda ganti dengan logika autentikasi, atau diarahkan ke Controller:
    // return [LoginController::class, 'login'];
});
// ----------------------------------------------------------------



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