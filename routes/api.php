<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserSkinCollectionController;
use App\Http\Controllers\SkinsController;


// Add a route outside of v1 for session-based auth
Route::get('/user', function () {
	if (Auth::check()) {
		return response()->json(['user' => Auth::user()]);
	}
	return response()->json(['user' => null], 401);
})->middleware('web');

Route::prefix('v1')->name('api.v1.')->group(function () {
	// CSRF cookie endpoint - required for Sanctum SPA auth
	Route::get('/sanctum/csrf-cookie', function () {
		return response()->json(['message' => 'CSRF cookie set']);
	})->middleware('web');

	// Auth routes with web middleware for Sanctum cookie-based auth
	Route::middleware('web')->group(function () {
		Route::post('/register', [AuthController::class, 'register'])->name('register');
		Route::post('/login', [AuthController::class, 'login'])->name('login');
		Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
	});

	// Dictionary public routes
	Route::get('/skins', [SkinsController::class, 'index'])->name('skins.index');

	Route::middleware('auth:sanctum')->group(function () {
		Route::get('/me', [AuthController::class, 'me'])->name('me');

		# user collection routes
		Route::get('/user/collection', [UserSkinCollectionController::class, 'index'])->name('user.collection.index');
		Route::post('/user/collection', [UserSkinCollectionController::class, 'store'])->name('user.collection.store');
		Route::delete('/user/collection/{id}', [UserSkinCollectionController::class, 'destroy'])->name('user.collection.destroy');
	});
});
