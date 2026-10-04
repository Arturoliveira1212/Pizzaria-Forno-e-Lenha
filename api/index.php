<?php

header('Content-Type: application/json');

use Dotenv\Dotenv;
use phputil\router\Router;

use function phputil\cors\cors;

require_once 'vendor/autoload.php';

try {
    $caminho = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH);

    if (
        is_string($caminho) && str_starts_with($caminho, '/assets/') && !str_contains($caminho, '..')
    ) {
        $arquivo = __DIR__ . $caminho;

        if (!is_file($arquivo)) {
            http_response_code(404);
            exit;
        }

        header('Content-Type: image/jpeg');
        readfile($arquivo);
        exit;
    }
} catch (Throwable $e) {
    http_response_code(404);
    exit;
}

try {
    $dotenv = Dotenv::createImmutable(__DIR__);
    $dotenv->load();

    $bancoDados = new BancoDados(PDOFactory::criar(
        nomeBanco: $_ENV['DB_NAME'],
        host: $_ENV['DB_HOST'],
        usuario: $_ENV['DB_USERNAME'],
        senha: $_ENV['DB_PASSWORD'],
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
