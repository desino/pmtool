<?php

use App\Http\Controllers\SocialiteController;
use Illuminate\Support\Facades\Route;


Route::controller(SocialiteController::class)->group(function(){
    Route::get('office-365-login/{provider}','redirectToProvider');
    Route::get('office-365-login/{provider}/callback', 'handleProviderCallback'); 
    Route::get('provider-callback-session-data','getProviderCallbackSessionData');   
});

Route::get('/{any}', function () {
    return view('welcome');
})->where('any', '.*');

Route::get('/password/reset/{token}', function () {
    return view('auth.passwords.reset');
})->name('password.reset');

