<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Auth\SocialiteController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function(){
    Route::controller(AuthController::class)->group(function () {
        Route::get('/user', 'user');
        // Route::get('/logout', 'logout');
    });
});

Route::controller(AuthController::class)->group(function () {
    Route::post('/login', 'login');
    Route::post('/forgot-password', 'sendResetLink');
    Route::post('/reset-password', 'resetPassword');
});

Route::controller(SocialiteController::class)->group(function(){
    Route::get('office-365-login/{provider}','redirectToProvider');
    Route::get('office-365-login/{provider}/callback', 'handleProviderCallback');
});

