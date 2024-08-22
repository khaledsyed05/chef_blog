<?php

use App\Http\Controllers\AuthApiController;
use Illuminate\Support\Facades\Route;



Route::prefix('auth')->group(function () {
    // Public routes
    Route::post('login', [AuthApiController::class, 'login']);
    Route::post('register', [AuthApiController::class, 'register']);
    Route::post('forgot-password', [AuthApiController::class, 'forgotPassword']);
    Route::post('reset-password', [AuthApiController::class, 'resetPassword']);

    // Protected routes
    Route::middleware(['auth:api','verified.email'])->group(function () {
        Route::post('logout', [AuthApiController::class, 'logout']);
        Route::get('user', [AuthApiController::class, 'getProfile']);
        Route::put('user', [AuthApiController::class, 'updateProfile']);
        Route::post('refresh', [AuthApiController::class, 'refreshToken']);
        Route::post('deactivate', [AuthApiController::class, 'deactivateAccount']);
        Route::middleware('auth.type:super-admin,admin')->group(function () {
            Route::post('reactivate', [AuthApiController::class, 'reactivateAccount']);
            Route::delete('permanent-delete', [AuthApiController::class, 'permanentlyDeleteAccount']);
            Route::get('deactivated-users', [AuthApiController::class, 'getDeactivatedUsers']);
        });
    });
});
Route::post('resend-email', [AuthApiController::class, 'resendVerificationEmail']);
Route::post('verify-email', [AuthApiController::class, 'verifyEmail']);
