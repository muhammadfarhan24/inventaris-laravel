<?php

return [
    'paths' => ['api/*', 'login', 'logout',],
    'allowed_methods' => ['*'],
    'allowed_origins' => ['http://localhost', 'http://127.0.0.1:5173', 'http://127.0.0.1:8000'],
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => false,
];
