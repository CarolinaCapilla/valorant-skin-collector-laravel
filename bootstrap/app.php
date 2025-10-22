<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
	->withRouting(
		web: __DIR__ . '/../routes/web.php',
		api: __DIR__ . '/../routes/api.php',
		commands: __DIR__ . '/../routes/console.php',
		health: '/up',
	)
	->withMiddleware(function (Middleware $middleware): void {
		// Exclude API routes from CSRF protection
		$middleware->validateCsrfTokens(except: [
			'api/*',
		]);

		// Disable redirect for unauthenticated API requests - return 401 JSON instead
		$middleware->redirectGuestsTo(function ($request) {
			// For API requests, don't redirect - let it return 401
			if ($request->is('api/*')) {
				return null;
			}
			// For web requests (if any), redirect to frontend login
			return env('FRONTEND_URL', 'http://localhost:3000') . '/auth/login';
		});
	})
	->withExceptions(function (Exceptions $exceptions): void {
		// Handle unauthenticated requests for API - return JSON instead of redirect
		$exceptions->renderable(function (\Illuminate\Auth\AuthenticationException $e, $request) {
			if ($request->is('api/*')) {
				return response()->json([
					'message' => 'Unauthenticated.'
				], 401);
			}
		});
	})->create();
