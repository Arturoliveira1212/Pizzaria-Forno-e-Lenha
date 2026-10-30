<?php

use phputil\router\HttpRequest;
use phputil\router\HttpResponse;
use phputil\router\Router;

return function (Router $app, BancoDados $bancoDados) {

    $app->post('/login', function (HttpRequest $request, HttpResponse $response) use ($bancoDados) {
        try {
            $dados = (array) $request->body();

            $gestorAutenticacao = GestorFactory::criarGestorAutenticacao($bancoDados);
            $usuario = $gestorAutenticacao->entrar(
                cpf: (string) ($dados['cpf'] ?? ''),
                senha: (string) ($dados['senha'] ?? ''),
                ip: $_SERVER['REMOTE_ADDR'] ?? '',
                navegador: $request->header('User-Agent') ?? '',
            );

            $response->status(200)->json(['message' => 'Login realizado com sucesso.', 'data' => [
                'usuario' => $usuario,
            ]]);
        } catch (DominioException $e) {
            $response->status(400)->json(['message' => 'Erro ao realizar login.', 'data' => [
                'erros' => $e->erros(),
            ]]);
        } catch (AutenticacaoException $e) {
            $response->status(401)->json(['message' => 'Erro ao realizar login.', 'data' => [
                'erros' => [$e->getMessage()],
            ]]);
        } catch (Throwable $e) {
            $response->status(500)->json(['message' => 'Houve um erro interno no servidor.']);
        }
    });

    $app->post('/logout', function (HttpRequest $request, HttpResponse $response) use ($bancoDados) {
        try {
            $gestorAutenticacao = GestorFactory::criarGestorAutenticacao($bancoDados);
            $gestorAutenticacao->sair();

            $response->status(200)->json(['message' => 'Logout realizado com sucesso.']);
        } catch (Throwable $e) {
            $response->status(500)->json(['message' => 'Houve um erro interno no servidor.']);
        }
    });

};
