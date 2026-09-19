<?php

use Dotenv\Dotenv;

require_once 'vendor/autoload.php';

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$pdo = PDOSingleton::obter();

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
            'name' => 'pizzaria',
            'connection' => $pdo,
        ],
    ],
    'version_order' => 'creation',
];
