<?php

/**
 * Database connection configuration.
 * Reads environment variables configured via Docker Compose.
 */
return [
    'class' => 'yii\db\Connection',
    'dsn' => sprintf(
        'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
        getenv('DB_HOST') ?: 'db',
        getenv('DB_PORT') ?: '3306',
        getenv('DB_NAME') ?: 'revpanda'
    ),
    'username' => getenv('DB_USER') ?: 'revpanda',
    'password' => getenv('DB_PASSWORD') ?: 'revpanda_pass',
    'charset' => 'utf8mb4',
    'enableSchemaCache' => false,
];
