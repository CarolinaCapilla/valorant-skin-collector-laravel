<?php
// config/cors.php -> 'paths' includes 'api/*', 'sanctum/csrf-cookie'
return [
    'paths' => ['api/*', 'sanctum/csrf-cookie', 'oauth/*'],
    'allowed_methods' => ['*'],
    'allowed_origins' => ['http://localhost:3000'], // your Nuxt dev origin
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => true,
];