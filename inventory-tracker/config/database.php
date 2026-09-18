<?php
declare(strict_types=1);

return [
    'default' => getenv('DB_DRIVER') ?: 'sqlite', // 'sqlite' or 'mysql'
    'sqlite' => [
        'path' => __DIR__ . '/../database/inventory.sqlite',
    ],
    'mysql' => [
        'host' => getenv('DB_HOST') ?: '127.0.0.1',
        'port' => getenv('DB_PORT') ?: '3306',
        'database' => getenv('DB_NAME') ?: 'inventory_tracker',
        'username' => getenv('DB_USER') ?: 'root',
        'password' => getenv('DB_PASS') ?: '',
        'charset' => 'utf8mb4',
    ],
];
