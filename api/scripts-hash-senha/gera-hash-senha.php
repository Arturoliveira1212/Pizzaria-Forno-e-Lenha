<?php

use Dotenv\Dotenv;

require_once dirname(__DIR__) . '/vendor/autoload.php';
Dotenv::createImmutable(dirname(__DIR__))->load();

$pimenta = $_ENV['PIMENTA'];
$senha = readline('Senha: ');
$hash = new GeradorHashSenha($pimenta)->gerar($senha);
$sal = explode('$', $hash)[4];

echo "Pimenta: {$pimenta}", PHP_EOL;
echo "Pimenta + senha: {$pimenta}{$senha}", PHP_EOL;
echo "Sal: {$sal}", PHP_EOL;
echo "Valor no banco: {$hash}", PHP_EOL;
