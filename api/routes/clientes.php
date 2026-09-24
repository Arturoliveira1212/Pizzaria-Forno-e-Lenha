<?php

use phputil\router\HttpRequest;
use phputil\router\HttpResponse;

$gestorCliente = new GestorCliente(new RepositorioClienteEmBDR($bancoDados));

$app->get('/clientes', function (HttpRequest $request, HttpResponse $response) use ($gestorCliente) {
    try {
        $busca = $request->param('busca') ?? '';
        $clientes = $gestorCliente->obterClientesPorNome($busca);

        $response->status(200)->json(['data' => $clientes, 'message' => 'Clientes obtidos com sucesso.']);
    } catch (Throwable $e) {
        $response->status(500)->json(['message' => 'Houve um erro interno no servidor.' . $e->getMessage()]);
    }
});
