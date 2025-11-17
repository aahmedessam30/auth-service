<?php

use App\Http\Controllers\Api\V1\Auth\LoginController;
use App\Http\Controllers\Api\V1\Auth\LogoutController;
use App\Http\Controllers\Api\V1\Auth\ProfileController;
use App\Http\Controllers\Api\V1\Auth\RefreshTokenController;
use App\Http\Controllers\Api\V1\Auth\RegisterController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Version 1 Routes
|--------------------------------------------------------------------------
|
| All routes registered here are prefixed with /api/v1
| These routes follow the standardized API response format.
|
*/

// Authentication routes
Route::group([
    'prefix' => 'auth',
    'as' => 'auth.',
], function (): void {
    Route::middleware('throttle:auth')->group(function (): void {
        Route::post('register', RegisterController::class)->name('register');
        Route::post('login', LoginController::class)->name('login');
        Route::post('refresh', RefreshTokenController::class)->name('refresh');
    });

    Route::middleware('auth.jwt')->group(function (): void {
        Route::get('profile', ProfileController::class)->name('profile');
        Route::post('logout', LogoutController::class)->name('logout');
    });
});
