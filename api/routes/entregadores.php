<?php

use phputil\router\HttpRequest;
use phputil\router\HttpResponse;

$gestorEntregador = new GestorEntregador(new RepositorioEntregadorEmBDR($bancoDados));

$app->get('/entregadores', function (HttpRequest $request, HttpResponse $response) use ($gestorEntregador) {
    try {
        $entregadores = $gestorEntregador->obterEntregadoresDisponiveis();

        $response->status(200)->json(['message' => 'Entregadores obtidos com sucesso.', 'data' => [
            'entregadores' => $entregadores,
        ]]);
    } catch (DominioException $e) {
        $response->status(400)->json(['message' => 'Erro ao obter entregadores.', 'data' => [
            'erros' => $e->erros(),
        ]]);
    } catch (Throwable $e) {
        $response->status(500)->json(['message' => 'Houve um erro interno no servidor.']);
    }
});
