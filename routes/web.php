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

    Route::resource('anggota', \App\Http\Controllers\Admin\AnggotaController::class);
});

Route::middleware('anggota')->prefix('anggota')->group(function () {
    Route::get('/', [\App\Http\Controllers\Anggota\HomeController::class, 'index']);

    Route::middleware('ketua')->prefix('ketua')->group(function () {
        // Route::resource('user', \App\Http\Controllers\Anggota\UserController::class);
    });
});
