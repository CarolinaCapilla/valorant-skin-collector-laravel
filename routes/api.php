<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserSkinCollectionController;
use App\Http\Controllers\SkinsController;

// Health check endpoint for monitoring/deployment verification
Route::get('/health', function () {
	return response()->json([
		'status' => 'healthy',
		'timestamp' => now()->toIso8601String(),
		'service' => 'valorant-skin-collector-api'
	]);
});

Route::prefix('v1')->name('api.v1.')->group(function () {
	// Public auth routes - no middleware needed for token-based auth
	Route::post('/register', [AuthController::class, 'register'])->name('register');
	Route::post('/login', [AuthController::class, 'login'])->name('login');

	// Public routes
	Route::get('/skins', [SkinsController::class, 'index'])->name('skins.index');

	// Protected routes - require Sanctum token authentication
	Route::middleware('auth:sanctum')->group(function () {
		Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
		Route::get('/me', [AuthController::class, 'me'])->name('me');

		# user collection routes
		Route::get('/user/collection', [UserSkinCollectionController::class, 'index'])->name('user.collection.index');
		Route::post('/user/collection', [UserSkinCollectionController::class, 'store'])->name('user.collection.store');
		Route::delete('/user/collection/skin', [UserSkinCollectionController::class, 'destroyBySkin'])->name('user.collection.destroy.skin');

		# wishlist routes
		Route::post('/user/wishlist', [UserSkinCollectionController::class, 'addToWishlist'])->name('user.wishlist.add');
		Route::delete('/user/wishlist/skin', [UserSkinCollectionController::class, 'removeFromWishlist'])->name('user.wishlist.remove');

		# favorite chroma routes
		Route::patch('/user/collection/favorite-chroma', [UserSkinCollectionController::class, 'updateFavoriteChroma'])->name('user.collection.update.favorite.chroma');
	});
});
