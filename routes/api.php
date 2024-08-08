<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Auth\SocialiteController;
use App\Http\Controllers\Api\ClientController;
use App\Http\Controllers\Api\HeaderController;
use App\Http\Controllers\Api\InitiativeController;
use App\Http\Controllers\Api\OpportunityController;
use App\Http\Controllers\Api\SolutionDesignController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function(){
    Route::controller(AuthController::class)->group(function () {
        Route::get('/user', 'user');
        // Route::get('/logout', 'logout');
    });

    Route::controller(ClientController::class)->prefix('client')->group(function () {
        Route::post('/store', 'store');
    });

    Route::controller(InitiativeController::class)->prefix('initiative')->group(function () {
        Route::get('/get-clients', 'getClients');
        Route::post('/store', 'store');
    });

    Route::controller(OpportunityController::class)->prefix('opportunity')->group(function () {
        Route::post('/', 'index');
        Route::get('/get-initial-data', 'getInitialData');
        Route::post('/update', 'update');
        Route::post('/update-status-lost', 'updateStatusLost');
    });

    Route::controller(SolutionDesignController::class)->prefix('solution-design')->group(function () {
        Route::post('/', 'index');
        Route::post('/get-initiative', 'getInitiative');
        Route::post('/store-section', 'storeSection');
        Route::post('/store-update-functionality', 'storeUpdateFunctionality');
        Route::post('/delete-functionality', 'deleteFunctionality');
        Route::post('/delete-section', 'deleteSection');
        Route::post('/update-section', 'updateSection');
    });

    Route::controller(HeaderController::class)->prefix('header')->group(function () {
       Route::get('/get-initiatives', 'getInitiatives');
    });
});

Route::controller(AuthController::class)->group(function () {
    Route::post('/login', 'login');
    Route::post('/logout', 'logout');
    Route::post('/forgot-password', 'sendResetLink');
    Route::post('/reset-password', 'resetPassword');
});