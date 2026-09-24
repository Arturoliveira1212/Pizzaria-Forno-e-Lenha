<?php

use Dotenv\Dotenv;

require_once 'vendor/autoload.php';

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

return [
    'paths' => [
        'migrations' => '%%PHINX_CONFIG_DIR%%/db/migrations',
        'seeds' => '%%PHINX_CONFIG_DIR%%/db/seeds',
    ],
    'templates' => [
        'file' => '%%PHINX_CONFIG_DIR%%/db/templates/migracao.php',
        'seedFile' => '%%PHINX_CONFIG_DIR%%/db/templates/semeadura.php',
    ],
    'environments' => [
        'default_migration_table' => 'phinxlog',
        'default_environment' => 'development',
        'development' => [
            'name' => $_ENV['DB_NAME'],
            'connection' => PDOFactory::criar(
                $_ENV['DB_NAME'],
                $_ENV['DB_HOST'],
                $_ENV['DB_USERNAME'],
                $_ENV['DB_PASSWORD'],
            ),
        ],
        'testing' => [
            'name' => $_ENV['DB_NAME_TEST'],
            'connection' => PDOFactory::criar(
                $_ENV['DB_NAME_TEST'],
                $_ENV['DB_HOST_TEST'],
                $_ENV['DB_USERNAME_TEST'],
                $_ENV['DB_PASSWORD_TEST'],
            ),
        ],
    ],
    'version_order' => 'creation',
];
