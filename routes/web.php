<?php

use Illuminate\Support\Facades\Route;

Route::get('/', [\App\Http\Controllers\AuthController::class, 'index']);

Route::get('login', [\App\Http\Controllers\AuthController::class, 'showLoginForm'])->name('login');
Route::post('login', [\App\Http\Controllers\AuthController::class, 'authenticate'])->name('login.authenticate');
Route::post('logout', [\App\Http\Controllers\AuthController::class, 'logout'])->middleware('auth');

Route::middleware(['auth', 'active'])->group(function () {
    Route::get('files/user-detail/{user}/{field}', [\App\Http\Controllers\FileController::class, 'userDetail'])->whereNumber('user')->name('files.user-detail');

    Route::get('files/pinjaman/{pinjaman}/{field}', [\App\Http\Controllers\FileController::class, 'pinjaman'])->whereNumber('pinjaman')->name('files.pinjaman');
});

// Route::middleware(['auth', 'active', 'admin'])->prefix('admin')->group(function () {
//     Route::get('/', [\App\Http\Controllers\Admin\HomeController::class, 'index']);
//     Route::get('anggota-set', [\App\Http\Controllers\Admin\HomeController::class, 'anggota_set']);

//     Route::post('anggota/reset_password/{id}', [\App\Http\Controllers\Admin\AnggotaController::class, 'reset_password']);
//     Route::resource('anggota', \App\Http\Controllers\Admin\AnggotaController::class);

//     Route::resource('simpanan', \App\Http\Controllers\Admin\SimpananController::class);

//     Route::resource('pinjaman', \App\Http\Controllers\Admin\PinjamanController::class);

//     Route::resource('pengaturan', \App\Http\Controllers\Admin\PengaturanController::class);
// });

Route::middleware(['auth', 'active', 'anggota'])
    ->prefix('anggota')
    ->group(function () {
        Route::get('/', [\App\Http\Controllers\Anggota\HomeController::class, 'index']);

        Route::get('profile', [\App\Http\Controllers\Anggota\HomeController::class, 'profile']);
        Route::post('profile', [\App\Http\Controllers\Anggota\HomeController::class, 'profile_proses']);

        Route::get('password', [\App\Http\Controllers\Anggota\HomeController::class, 'password']);
        Route::post('password', [\App\Http\Controllers\Anggota\HomeController::class, 'password_proses']);

        Route::get('notifikasi', [\App\Http\Controllers\Anggota\HomeController::class, 'notifikasi']);
    });

Route::middleware(['auth', 'active', 'admin'])->prefix('admin')->group(function () {

    Route::get('/', [
        \App\Http\Controllers\Admin\HomeController::class,
        'index',
    ])->name('admin.home');

    Route::prefix('users')
        ->name('admin.users.')
        ->group(function () {

            Route::get('/', [
                \App\Http\Controllers\Admin\UserController::class,
                'index',
            ])->name('index');

            Route::get('/create', [
                \App\Http\Controllers\Admin\UserController::class,
                'create',
            ])->name('create');

            Route::post('/', [
                \App\Http\Controllers\Admin\UserController::class,
                'store',
            ])->name('store');

            Route::get('/{user}/edit', [
                \App\Http\Controllers\Admin\UserController::class,
                'edit',
            ])->name('edit');

            Route::put('/{user}', [
                \App\Http\Controllers\Admin\UserController::class,
                'update',
            ])->name('update');

            Route::patch('/{user}/toggle-status', [
                \App\Http\Controllers\Admin\UserController::class,
                'toggleStatus',
            ])->name('toggle-status');

            Route::patch('/{user}/reset-password', [
                \App\Http\Controllers\Admin\UserController::class,
                'resetPassword',
            ])->name('reset-password');
        });

    Route::prefix('loan-settings')
        ->name('admin.loan-settings.')
        ->group(function () {
            Route::get('/', [
                \App\Http\Controllers\Admin\LoanSettingController::class,
                'edit',
            ])->name('edit');

            Route::put('/', [
                \App\Http\Controllers\Admin\LoanSettingController::class,
                'update',
            ])->name('update');
        });

    Route::prefix('accounts')
        ->name('admin.accounts.')
        ->group(function () {

            Route::get('/', [
                \App\Http\Controllers\Admin\AccountController::class,
                'index',
            ])->name('index');

            Route::get('/create', [
                \App\Http\Controllers\Admin\AccountController::class,
                'create',
            ])->name('create');

            Route::post('/', [
                \App\Http\Controllers\Admin\AccountController::class,
                'store',
            ])->name('store');

            Route::get('/{account}/edit', [
                \App\Http\Controllers\Admin\AccountController::class,
                'edit',
            ])->name('edit');

            Route::get('/{account}/initialize-opening-balance', [
                \App\Http\Controllers\Admin\AccountController::class,
                'showInitializeOpeningBalance',
            ])->name('initialize-opening-balance.form');

            Route::put('/{account}', [
                \App\Http\Controllers\Admin\AccountController::class,
                'update',
            ])->name('update');

            Route::patch('/{account}/initialize-opening-balance', [
                \App\Http\Controllers\Admin\AccountController::class,
                'initializeOpeningBalance',
            ])->name('initialize-opening-balance');

            Route::patch('/{account}/toggle-status', [
                \App\Http\Controllers\Admin\AccountController::class,
                'toggleStatus',
            ])->name('toggle-status');
        });
});

Route::middleware(['auth', 'active'])
    ->prefix('member')
    ->name('member.')
    ->group(function () {

        Route::get('/', [\App\Http\Controllers\Member\HomeController::class, 'index'])
            ->name('home');

        Route::prefix('profile')
            ->name('profile.')
            ->group(function () {
                Route::get('/', [\App\Http\Controllers\Member\ProfileController::class, 'index'])
                    ->name('index');

                Route::get('/edit', [\App\Http\Controllers\Member\ProfileController::class, 'edit'])
                    ->name('edit');

                Route::put('/', [\App\Http\Controllers\Member\ProfileController::class, 'update'])
                    ->name('update');
            });

        Route::prefix('loans')
            ->name('loans.')
            ->group(function () {
                Route::get('/', [\App\Http\Controllers\Member\LoanController::class, 'index'])
                    ->name('index');

                Route::get('/create', [\App\Http\Controllers\Member\LoanController::class, 'create'])
                    ->name('create');

                Route::post('/', [\App\Http\Controllers\Member\LoanController::class, 'store'])
                    ->name('store');

                Route::post('/{loan}/top-up', [\App\Http\Controllers\Member\LoanController::class, 'storeTopUp'])
                    ->name('top-up.store');

                Route::get('/{loan}', [\App\Http\Controllers\Member\LoanController::class, 'show'])
                    ->name('show');
            });
    });

Route::middleware(['auth', 'active'])
    ->prefix('analyst-manager')
    ->name('analyst-manager.')
    ->group(function () {

        Route::get('/', [
            \App\Http\Controllers\AnalystManager\HomeController::class,
            'index',
        ])->name('home');

        Route::get('/loans', [
            \App\Http\Controllers\AnalystManager\LoanController::class,
            'index'
        ])->name('loans.index');

        Route::get('/loans/{loan}', [
            \App\Http\Controllers\AnalystManager\LoanController::class,
            'show'
        ])->name('loans.show');

        Route::post('/loans/{loan}/analysis', [
            \App\Http\Controllers\AnalystManager\LoanController::class,
            'analyze'
        ])->name('loans.analysis');

        Route::get('/loans/{loan}/documents', [
            \App\Http\Controllers\AnalystManager\LoanController::class,
            'documents',
        ])->name('loans.documents');

        Route::post('/loans/{loan}/documents/generate', [
            \App\Http\Controllers\AnalystManager\LoanController::class,
            'generateDocuments',
        ])->name('loans.documents.generate');

        Route::post('/loans/{loan}/documents/upload', [
            \App\Http\Controllers\AnalystManager\LoanController::class,
            'uploadDocuments',
        ])->name('loans.documents.upload');

        Route::post('/loans/{loan}/documents/approval-letter', [
            \App\Http\Controllers\AnalystManager\LoanController::class,
            'generateApprovalLetter',
        ])->name('loans.documents.approval-letter');

        Route::post('/loans/{loan}/documents/credit-agreement', [
            \App\Http\Controllers\AnalystManager\LoanController::class,
            'generateCreditAgreement',
        ])->name('loans.documents.credit-agreement');

        Route::get('/loans/{loan}/documents/{document}/view', [
            \App\Http\Controllers\AnalystManager\LoanController::class,
            'viewGeneratedDocument',
        ])->name('loans.documents.view');

        Route::get('/loans/{loan}/documents/{document}/download', [
            \App\Http\Controllers\AnalystManager\LoanController::class,
            'downloadGeneratedDocument',
        ])->name('loans.documents.download');

        Route::post('/loans/{loan}/analysis/start', [
            \App\Http\Controllers\AnalystManager\LoanController::class,
            'startAnalysis',
        ])->name('loans.analysis.start');

        Route::post('/loans/{loan}/analysis', [
            \App\Http\Controllers\AnalystManager\LoanController::class,
            'analyze',
        ])->name('loans.analysis');
    });

Route::middleware(['auth', 'active'])
    ->prefix('treasurer')
    ->name('treasurer.')
    ->group(function () {

        Route::get('/', [
            \App\Http\Controllers\Treasurer\HomeController::class,
            'index',
        ])->name('home');

        Route::get('/loans', [
            \App\Http\Controllers\Treasurer\LoanController::class,
            'index',
        ])->name('loans.index');

        Route::get('/loans/{loan}', [
            \App\Http\Controllers\Treasurer\LoanController::class,
            'show',
        ])->name('loans.show');

        Route::post('/loans/{loan}/review', [
            \App\Http\Controllers\Treasurer\LoanController::class,
            'review',
        ])->name('loans.review');

        Route::get('/disbursements', [
            \App\Http\Controllers\Treasurer\LoanController::class,
            'disbursements',
        ])->name('disbursements.index');

        Route::get('/disbursements/history', [
            \App\Http\Controllers\Treasurer\LoanController::class,
            'disbursementHistory',
        ])->name('disbursements.history');

        Route::get('/disbursements/{loan}', [
            \App\Http\Controllers\Treasurer\LoanController::class,
            'showDisbursement',
        ])->name('disbursements.show');

        Route::post('/disbursements/{loan}', [
            \App\Http\Controllers\Treasurer\LoanController::class,
            'disburse',
        ])->name('disbursements.store');

        Route::get(
            '/installments',
            [App\Http\Controllers\Treasurer\InstallmentController::class, 'index']
        )->name('installments.index');

        Route::get(
            '/installments/{loan}',
            [App\Http\Controllers\Treasurer\InstallmentController::class, 'show']
        )->name('installments.show');

        Route::post(
            '/installments/{installment}/pay',
            [App\Http\Controllers\Treasurer\InstallmentController::class, 'pay']
        )->name('installments.pay');
    });

Route::middleware(['auth', 'active'])
    ->prefix('chairman')
    ->name('chairman.')
    ->group(function () {

        Route::get('/', [
            \App\Http\Controllers\Chairman\HomeController::class,
            'index',
        ])->name('home');

        Route::get('/loans', [
            \App\Http\Controllers\Chairman\LoanController::class,
            'index',
        ])->name('loans.index');

        Route::get('/loans/{loan}', [
            \App\Http\Controllers\Chairman\LoanController::class,
            'show',
        ])->name('loans.show');

        Route::post('/loans/{loan}/approve', [
            \App\Http\Controllers\Chairman\LoanController::class,
            'approve',
        ])->name('loans.approve');
    });

Route::middleware(['auth', 'active'])
    ->prefix('secretary')
    ->name('secretary.')
    ->group(function () {

        Route::get('/', [
            \App\Http\Controllers\Secretary\HomeController::class,
            'index',
        ])->name('home');

        Route::get('/loans', [
            \App\Http\Controllers\Secretary\LoanController::class,
            'index',
        ])->name('loans.index');

        Route::get('/loans/{loan}', [
            \App\Http\Controllers\Secretary\LoanController::class,
            'show',
        ])->name('loans.show');

        Route::get('/loans/{loan}/documents/{document}/generated', [
            \App\Http\Controllers\Secretary\LoanController::class,
            'viewGeneratedDocument',
        ])->name('loans.documents.generated');

        Route::get('/loans/{loan}/documents/{document}/signed', [
            \App\Http\Controllers\Secretary\LoanController::class,
            'viewSignedDocument',
        ])->name('loans.documents.signed');

        Route::post('/loans/{loan}/documents/{document}/verify', [
            \App\Http\Controllers\Secretary\LoanController::class,
            'verifyDocument',
        ])->name('loans.documents.verify');

        Route::post('/loans/{loan}/documents/{document}/reject', [
            \App\Http\Controllers\Secretary\LoanController::class,
            'rejectDocument',
        ])->name('loans.documents.reject');

        Route::post('/loans/{loan}/collaterals/{collateral}/verify', [
            \App\Http\Controllers\Secretary\LoanController::class,
            'verifyCollateral',
        ])->name('loans.collaterals.verify');

        Route::post('/loans/{loan}/collaterals/{collateral}/reject', [
            \App\Http\Controllers\Secretary\LoanController::class,
            'rejectCollateral',
        ])->name('loans.collaterals.reject');
    });
