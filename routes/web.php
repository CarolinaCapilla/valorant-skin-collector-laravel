<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SocialAuthController;

Route::get('/', function () {
	return response()->json([
		'name' => 'Valorant Skin Collector API',
		'version' => '1.0.0',
		'status' => 'running'
	]);
});

Route::get('/auth/{provider}/redirect', [SocialAuthController::class, 'redirect']);
Route::get('/auth/{provider}/callback', [SocialAuthController::class, 'callback']);
