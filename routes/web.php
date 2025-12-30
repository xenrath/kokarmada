<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

Route::get('/storage-link', function () {
    Artisan::call('storage:link');
    return "storage link success";
});

Route::get('/optimize-clear', function () {
    Artisan::call('optimize:clear');
    return "optimize clear success";
});

Route::get('/', [\App\Http\Controllers\AuthController::class, 'index']);
Route::get('login', [\App\Http\Controllers\AuthController::class, 'login']);
Route::post('login', [\App\Http\Controllers\AuthController::class, 'login_proses']);
Route::post('logout', [\App\Http\Controllers\AuthController::class, 'logout'])->middleware('auth');

Route::middleware('admin')->prefix('admin')->group(function () {
    Route::get('/', [\App\Http\Controllers\Admin\HomeController::class, 'index']);

    Route::post('anggota/reset_password/{id}', [\App\Http\Controllers\Admin\AnggotaController::class, 'reset_password']);
    Route::resource('anggota', \App\Http\Controllers\Admin\AnggotaController::class);

    Route::resource('simpanan', \App\Http\Controllers\Admin\SimpananController::class);

    Route::resource('pinjaman', \App\Http\Controllers\Admin\PinjamanController::class);
});

Route::middleware('anggota')->prefix('anggota')->group(function () {
    Route::get('/', [\App\Http\Controllers\Anggota\HomeController::class, 'index']);
    Route::get('profile', [\App\Http\Controllers\Anggota\HomeController::class, 'profile']);
    Route::post('profile', [\App\Http\Controllers\Anggota\HomeController::class, 'profile_proses']);

    Route::middleware('ketua')->prefix('ketua')->group(function () {
        // Route::resource('user', \App\Http\Controllers\Anggota\UserController::class);
    });
});
