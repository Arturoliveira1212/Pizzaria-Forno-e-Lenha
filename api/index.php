<?php

header('Content-Type: application/json');

use Dotenv\Dotenv;
use phputil\router\Router;

use function phputil\cors\cors;

require_once 'vendor/autoload.php';

try {
    $dotenv = Dotenv::createImmutable(__DIR__);
    $dotenv->load();

    $nomeAmbiente = getenv('APP_ENV') ?: ($_ENV['APP_ENV'] ?? 'development');
    $ambiente = $nomeAmbiente === 'testing' ? 'TEST' : '';
    $sufixo = $ambiente === 'TEST' ? '_TEST' : '';

    $bancoDados = new BancoDados(PDOFactory::criar(
        nomeBanco: $_ENV["DB_NAME{$sufixo}"],
        host: $_ENV["DB_HOST{$sufixo}"],
        usuario: $_ENV["DB_USERNAME{$sufixo}"],
        senha: $_ENV["DB_PASSWORD{$sufixo}"],
    ));

    $app = new Router();

    $app->use(cors([
        'origin' => ['http://localhost:5174', 'http://localhost:5173'],
        'credentials' => true,
        'allowedHeaders' => ['Accept', 'Authorization', 'Cookie', 'Content-Length', 'Content-Type', 'Host', 'Origin', 'Referer'],
        'exposeHeaders' => ['Content-Length', 'Content-Type', 'Set-Cookie'],
        'maxAge' => 3600,
    ]));

    $caminhos = glob('routes/*.php') ?: [];
    foreach ($caminhos as $arquivo) {
        require_once $arquivo;
    }

    $app->listen();
} catch (Throwable $e) {
    http_response_code(500);
    die(json_encode(['erro' => 'Houve um erro interno no servidor.']));
}
