<?php

use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/{any}', function () {
    return view('welcome');
})->where('any', '.*');

Route::get('/password/reset/{token}', function () {
    return view('auth.passwords.reset');
})->name('password.reset');

// Route::get('{view}', ApplicationController::class)->where('view','(.*)');
