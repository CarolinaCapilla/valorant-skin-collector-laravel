<?php

return [
	'paths' => ['api/*', 'sanctum/csrf-cookie'],

	'allowed_methods' => ['*'],

	// Support multiple frontend origins (local + production)
	// Set FRONTEND_URL env var as comma-separated list: http://localhost:3000,https://yourapp.vercel.app
	'allowed_origins' => array_filter(explode(',', env('FRONTEND_URL', 'http://localhost:3000'))),

	'allowed_origins_patterns' => [],

	'allowed_headers' => ['*'],

	'exposed_headers' => [],

	'max_age' => 0,

	// Changed to false for token-based authentication (not cookie-based)
	'supports_credentials' => false,
];
