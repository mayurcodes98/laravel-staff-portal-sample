<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\DocumentController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\OtpRequestController;
use App\Http\Controllers\Auth\OtpVerifyController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public authentication routes
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function (): void {
    Route::get('/login', [OtpRequestController::class, 'show'])->name('otp.request.show');
    Route::post('/login', [OtpRequestController::class, 'store'])
        ->middleware('throttle:'.config('otp.rate_limit_per_minute').',1')
        ->name('otp.request.store');

    Route::get('/verify', [OtpVerifyController::class, 'show'])->name('otp.verify.show');
    Route::post('/verify', [OtpVerifyController::class, 'store'])->name('otp.verify.store');
});

Route::middleware('auth')->group(function (): void {
    Route::get('/dashboard', fn () => view('dashboard'))->name('dashboard');

    Route::post('/logout', function () {
        auth()->logout();
        request()->session()->invalidate();

        return redirect()->route('otp.request.show');
    })->name('logout');
});

/*
|--------------------------------------------------------------------------
| Admin routes
|--------------------------------------------------------------------------
*/
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'role:admin'])
    ->group(function (): void {
        Route::resource('documents', DocumentController::class)
            ->only(['index', 'create', 'store', 'destroy']);

        Route::get('users', [UserController::class, 'index'])
            ->name('users.index');
    });

/*
|--------------------------------------------------------------------------
| Employee routes
|--------------------------------------------------------------------------
*/
Route::prefix('my')
    ->name('employee.')
    ->middleware(['auth', 'role:employee'])
    ->group(function (): void {
        Route::get('documents', [App\Http\Controllers\Employee\DocumentController::class, 'index'])
            ->name('documents.index');
        Route::get('documents/{document}/download', [App\Http\Controllers\Employee\DocumentController::class, 'download'])
            ->name('documents.download');
    });
