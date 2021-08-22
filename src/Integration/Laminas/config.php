<?php

return [
    'doctrine' => [
        'default' => getenv('DB_CONNECTION', 'mysql'),

        'connections' => [
            'mysql' => [
                // Main connection configuration
                'driver' => 'pdo_mysql',
                'host' => getenv('DB_HOST', '127.0.0.1'),
                'port' => getenv('DB_PORT', '3306'),
                'username' => getenv('DB_USERNAME', 'user'),
                'password' => getenv('DB_PASSWORD', ''),
                'database' => getenv('DB_DATABASE', 'test'),

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

        // This is used by the doctrine commands so can't use laravel to locate the database directory
        'proxies' => getcwd() . '/database/proxies',
    ],
];
