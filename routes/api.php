<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function(){
    Route::controller(AuthController::class)->group(function () {
        Route::get('/user', 'user');
    });    
});

Route::controller(AuthController::class)->group(function () {
    Route::post('/login', 'login'); 
});

