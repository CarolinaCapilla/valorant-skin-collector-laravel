<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SocialAuthController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/oauth/{provider}/redirect', [SocialAuthController::class, 'redirect']);
Route::get('/oauth/{provider}/callback', [SocialAuthController::class, 'callback']);
