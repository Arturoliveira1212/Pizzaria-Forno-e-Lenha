<?php

use Dotenv\Dotenv;

require_once 'vendor/autoload.php';

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

function criarBanco(string $usuario, string $senha, string $banco): void {
    system("mysql -u {$usuario} --password={$senha} -e \"CREATE DATABASE IF NOT EXISTS {$banco}\" 2>&1");
    echo "Banco {$banco} ok\n";
}

criarBanco($_ENV['DB_USERNAME'], $_ENV['DB_PASSWORD'], $_ENV['DB_NAME']);
criarBanco($_ENV['DB_USERNAME_TEST'], $_ENV['DB_PASSWORD_TEST'], $_ENV['DB_NAME_TEST']);
