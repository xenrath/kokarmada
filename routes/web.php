<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

Route::get('/migrate-fresh-seed', function () {
    Artisan::call('migrate:fresh --seed');
    return "migrate fresh and seed success";
});

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
    Route::get('anggota-set', [\App\Http\Controllers\Admin\HomeController::class, 'anggota_set']);

    Route::post('anggota/reset_password/{id}', [\App\Http\Controllers\Admin\AnggotaController::class, 'reset_password']);
    Route::resource('anggota', \App\Http\Controllers\Admin\AnggotaController::class);

    Route::resource('simpanan', \App\Http\Controllers\Admin\SimpananController::class);

    Route::resource('pinjaman', \App\Http\Controllers\Admin\PinjamanController::class);

    Route::resource('pengaturan', \App\Http\Controllers\Admin\PengaturanController::class);
});

Route::middleware('anggota')->prefix('anggota')->group(function () {
    Route::get('/', [\App\Http\Controllers\Anggota\HomeController::class, 'index']);
    Route::get('profile', [\App\Http\Controllers\Anggota\HomeController::class, 'profile']);
    Route::post('profile', [\App\Http\Controllers\Anggota\HomeController::class, 'profile_proses']);
    Route::get('password', [\App\Http\Controllers\Anggota\HomeController::class, 'password']);
    Route::post('password', [\App\Http\Controllers\Anggota\HomeController::class, 'password_proses']);

    Route::resource('simpanan', \App\Http\Controllers\Anggota\SimpananController::class);

    Route::resource('pinjaman', \App\Http\Controllers\Anggota\PinjamanController::class);

    Route::middleware('ketua')->prefix('ketua')->group(function () {
        Route::resource('simpanan', \App\Http\Controllers\Anggota\Ketua\SimpananController::class);

        Route::resource('pinjaman', \App\Http\Controllers\Anggota\Ketua\PinjamanController::class);
    });

    Route::middleware('manajer')->prefix('manajer')->group(function () {
        Route::resource('simpanan', \App\Http\Controllers\Anggota\Manajer\SimpananController::class);

        Route::get('pinjaman/print/{id}', [\App\Http\Controllers\Anggota\Manajer\PinjamanController::class, 'print']);
        Route::resource('pinjaman', \App\Http\Controllers\Anggota\Manajer\PinjamanController::class);
    });
});
