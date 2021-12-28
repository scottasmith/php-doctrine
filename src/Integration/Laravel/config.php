<?php

return [
    'default' => env('DB_CONNECTION', 'mysql'),

    'connections' => [
        'mysql' => [
            // Main connection configuration
            'driver' => 'pdo_mysql',
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'username' => env('DB_USERNAME', 'user'),
            'password' => env('DB_PASSWORD', ''),
            'database' => env('DB_DATABASE', 'test'),

            // Set the connection logger
            // 'logger' => \ScottSmith\Doctrine\Logger\PsrLogger::class,

            // Entity paths
            'paths' => [
                // 'app/Entities',
            ],

            // Migrations
            'migration_paths' => [
                'App\DoctrineMigrations' => getcwd() . '/database/doctrine_migrations/mysql',
            ],
        ],
    ],

    'cache' => (env('APP_ENV') == 'production')
        ? new \ScottSmith\Doctrine\Caching\FilesystemCachingProvider(dirname(__DIR__) . '/storage/doctrine/cache')
        : null,

    'proxies' => (env('APP_ENV') === 'production') ? dirname(__DIR__) . '/storage/doctrine/proxies' : null,
];

