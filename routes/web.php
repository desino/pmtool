<?php

use App\Http\Controllers\Api\Auth\SocialiteController;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::controller(SocialiteController::class)->group(function(){
    Route::get('office-365-login/{provider}','redirectToProvider');
    // Route::get('api/office-365-login/{provider}/callback', 'handleProviderCallback');    
});

Route::get('/{any}', function () {
    return view('welcome');
})->where('any', '.*');

Route::get('/password/reset/{token}', function () {
    return view('auth.passwords.reset');
})->name('password.reset');

// Route::get('{view}', ApplicationController::class)->where('view','(.*)');

