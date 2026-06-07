<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Auth\ProfileController;
use App\Http\Controllers\Api\V1\UserController;

Route::prefix('v1')->group(function () {

    // ──────────────────────────────────────────────────────────
    // Auth pública
    // ──────────────────────────────────────────────────────────
    Route::prefix('auth')->group(function () {

        // Rate limit: 10 intentos por minuto por IP
        Route::middleware('throttle:10,1')->group(function () {
            Route::post('register', [AuthController::class, 'register']);
            Route::post('login',    [AuthController::class, 'login']);
        });

        // Verificación de email (enlace firmado, no requiere token)
        Route::get(
            'email/verify/{id}/{hash}',
            [EmailVerificationController::class, 'verify']
        )->name('verification.verify')->middleware('signed');

        // Password reset (rate limit: 5 por minuto)
        Route::middleware('throttle:5,1')->group(function () {
            Route::post('password/forgot', [PasswordResetController::class, 'forgot']);
            Route::post('password/reset',  [PasswordResetController::class, 'reset']);
        });

        // ──────────────────────────────────────────────────────
        // Rutas autenticadas — access token
        // ──────────────────────────────────────────────────────
        Route::middleware(['auth:sanctum', 'abilities:access'])->group(function () {
            Route::get('me',     [AuthController::class, 'me']);
            Route::post('logout', [AuthController::class, 'logout']);

            // Reenviar verificación de email
            Route::post('email/resend', [EmailVerificationController::class, 'resend'])
                ->middleware('throttle:3,1');
        });

        // ──────────────────────────────────────────────────────
        // Refresh token — solo acepta tokens con ability 'refresh'
        // ──────────────────────────────────────────────────────
        Route::middleware(['auth:sanctum', 'abilities:refresh'])->group(function () {
            Route::post('refresh', [AuthController::class, 'refresh']);
        });
    });

    // ──────────────────────────────────────────────────────────
    // Perfil — requiere access token + email verificado
    // ──────────────────────────────────────────────────────────
    Route::middleware(['auth:sanctum', 'abilities:access', 'verified'])->prefix('profile')->group(function () {
        Route::get('/',          [ProfileController::class, 'show']);
        Route::put('/',          [ProfileController::class, 'update']);
        Route::put('/password',  [ProfileController::class, 'changePassword']);
        Route::delete('/',       [ProfileController::class, 'destroy']);
    });

    // ──────────────────────────────────────────────────────────
    // Rutas protegidas + email verificado
    // ──────────────────────────────────────────────────────────
    Route::middleware(['auth:sanctum', 'abilities:access', 'verified'])->group(function () {

        // Solo admins
        Route::middleware('role:admin')->group(function () {
            Route::get('users',      [UserController::class, 'index']);
            Route::get('users/{id}', [UserController::class, 'show']);
        });

    });

});
