<?php

return [
    'default' => env('DB_CONNECTION', 'pgsql'),
    'connections' => [
        'pgsql' => [
            'driver' => 'pgsql',
            'host' => env('DB_HOST', 'localhost'),
            'port' => env('DB_PORT', '5432'),
            'database' => env('DB_DATABASE', 'autoparts-db'),
            'username' => env('DB_USERNAME', 'user'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => 'utf8',
            'schema' => env('DB_SCHEMA', 'public'),
        ],
    ],
];
