<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserSkinController;
use App\Http\Controllers\SkinsController;


Route::prefix('v1')->name('api.v1.')->group(function () {
	// Auth routes
	Route::post('/register', [AuthController::class, 'register'])->name('register');
	Route::post('/login', [AuthController::class, 'login'])->name('login');
	Route::post('/logout', [AuthController::class, 'logout'])->name('logout'); // optional, can be protected
	// Dictionary public routes
	Route::get('/skins', [SkinsController::class, 'index'])->name('skins.index');

	Route::middleware('auth:sanctum')->group(function () {
		Route::get('/me', [AuthController::class, 'me'])->name('me');

		# user skins
		Route::get('/user/skins', [UserSkinController::class, 'index'])->name('user.skins.index');
		Route::post('/user/skins', [UserSkinController::class, 'store'])->name('user.skins.store');
		Route::delete('/user/skins/{id}', [UserSkinController::class, 'destroy'])->name('user.skins.destroy');
	});
});
