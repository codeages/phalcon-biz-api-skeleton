<?php

return [
    'debug' => env('DEBUG', false),
    'db.options' => [
        'dbname' => env('DB_NAME', 'example_db'),
        'user' => env('DB_USER', 'root'),
        'password' => env('DB_PASSWORD', ''),
        'host' => env('DB_HOST', '127.0.0.1'),
        'port' => env('DB_PORT', '3306'),
        'driver' => 'pdo_mysql',
        'charset' => 'utf8',
    ],
    'log_dir' => dirname(__DIR__).'/var/log',
    'cache_directory' => dirname(__DIR__).'/var/cache',
    'tmp_directory' => dirname(__DIR__).'/var/tmp',
    'jsonrpc_endpoints' => [
        'example' => [
            'addr' => env('JSONRPC_EXAMPLE_ADDR'),
            'auth_type' => 'basic',
            'auth_credentials' => [
                'username' => env('JSONRPC_EXAMPLE_USERNAME'),
                'password' => env('JSONRPC_EXAMPLE_PASSWORD'),
            ]
        ],
    ]
];
