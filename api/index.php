<?php

use Dotenv\Dotenv;
use phputil\router\Router;

use function phputil\cors\cors;

require_once 'vendor/autoload.php';

try {
    $dotenv = Dotenv::createImmutable(__DIR__);
    $dotenv->load();

    $bancoDados = new BancoDados(PDOFactory::criar(
        $_ENV['DB_NAME'],
        $_ENV['DB_HOST'],
        $_ENV['DB_USERNAME'],
        $_ENV['DB_PASSWORD'],
    ));

    $app = new Router();

    $app->use(cors([
        'origin' => ['http://localhost:8080', 'http://localhost:5173'],
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
