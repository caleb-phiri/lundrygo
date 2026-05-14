<?php

use App\Http\Controllers\Api\AuthController;

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);
Route::get('/verify-email/{id}/{hash}', [AuthController::class, 'verifyEmail'])->name('verification.verify');

// Protected routes
Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::put('/profile', [AuthController::class, 'updateProfile']);
    Route::post('/refresh-token', [AuthController::class, 'refreshToken']);
    Route::post('/change-password', [AuthController::class, 'changePassword']);
    Route::post('/resend-verification', [AuthController::class, 'resendVerification']);
    Route::delete('/account', [AuthController::class, 'deleteAccount']);
    Route::get('/sessions', [AuthController::class, 'getSessions']);
    Route::delete('/sessions/{tokenId}', [AuthController::class, 'revokeSession']);
});