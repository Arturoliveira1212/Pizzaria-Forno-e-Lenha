<?php

use phputil\router\HttpRequest;
use phputil\router\HttpResponse;

$gestorCliente = new GestorCliente(new RepositorioClienteEmBDR($bancoDados));

$app->get('/clientes', function (HttpRequest $request, HttpResponse $response) use ($gestorCliente) {
    try {
        $restricoes = RestricoesParaBuscaDeClientes::criarComArray($request->queries() ?? []);
        $clientes = $gestorCliente->obterClientes($restricoes);

        $response->status(200)->json(['message' => 'Clientes obtidos com sucesso.', 'data' => [
            'clientes' => $clientes,
        ]]);
    } catch (DominioException $e) {
        $response->status(400)->json(['message' => 'Erro ao obter clientes.', 'data' => [
            'erros' => $e->erros(),
        ]]);
    } catch (Throwable $e) {
        $response->status(500)->json(['message' => 'Houve um erro interno no servidor.']);
    }
});
