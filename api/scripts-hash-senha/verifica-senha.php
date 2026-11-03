<?php

use Dotenv\Dotenv;

require_once dirname(__DIR__) . '/vendor/autoload.php';
Dotenv::createImmutable(dirname(__DIR__))->load();

$pimenta = $_ENV['PIMENTA'];
$valorNoBanco = readline('Valor no banco: ');
$senha = readline('Senha: ');

[, , , $parametros, $sal, $hash] = explode('$', $valorNoBanco);
parse_str(str_replace(',', '&', $parametros), $custo);
$hashCalculado = sodium_crypto_pwhash(
    32,
    $pimenta . $senha,
    base64_decode($sal),
    (int) $custo['t'],
    (int) $custo['m'] * 1024,
    SODIUM_CRYPTO_PWHASH_ALG_ARGON2ID13,
);
$hashCalculado = rtrim(base64_encode($hashCalculado), '=');
$senhaCorreta = new GeradorHashSenha($pimenta)->verificar($senha, $valorNoBanco);

echo "Sal: {$sal}", PHP_EOL;
echo "Pimenta + senha: {$pimenta}{$senha}", PHP_EOL;
echo "Hash calculado: {$hashCalculado}", PHP_EOL;
echo "Hash no banco:  {$hash}", PHP_EOL;
echo 'Resultado: ', $senhaCorreta ? 'senha correta' : 'senha incorreta', PHP_EOL;
