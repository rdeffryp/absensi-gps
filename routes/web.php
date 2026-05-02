<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Login & Logout
Route::get('/', fn() => redirect()->route('login'));
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Admin Routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/karyawan', [AdminController::class, 'karyawan'])->name('karyawan');
    Route::post('/karyawan', [AdminController::class, 'karyawanStore'])->name('karyawan.store');
    Route::put('/karyawan/{user}', [AdminController::class, 'karyawanUpdate'])->name('karyawan.update');
    Route::delete('/karyawan/{user}', [AdminController::class, 'karyawanDestroy'])->name('karyawan.destroy');
    Route::get('/rekap', [AdminController::class, 'rekap'])->name('rekap');
    Route::get('/rekap/export', [AdminController::class, 'export'])->name('rekap.export');
    Route::get('/settings', [AdminController::class, 'settings'])->name('settings');
    Route::put('/settings', [AdminController::class, 'settingsUpdate'])->name('settings.update');
});
// Profile Routes (admin & karyawan)
Route::middleware(['auth'])->prefix('profile')->name('profile.')->group(function () {
    Route::get('/', [ProfileController::class, 'index'])->name('index');
    Route::post('/update', [ProfileController::class, 'update'])->name('update');
    Route::post('/password', [ProfileController::class, 'updatePassword'])->name('password');
});
// Karyawan Routes
Route::middleware(['auth', 'role:karyawan'])->prefix('karyawan')->name('karyawan.')->group(function () {
    Route::get('/dashboard', [KaryawanController::class, 'dashboard'])->name('dashboard');
    Route::post('/absen', [KaryawanController::class, 'absen'])->name('absen');
    Route::get('/riwayat', [KaryawanController::class, 'riwayat'])->name('riwayat');
});