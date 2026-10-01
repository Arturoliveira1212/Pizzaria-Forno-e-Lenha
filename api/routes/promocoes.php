<?php

use phputil\router\HttpRequest;
use phputil\router\HttpResponse;

$gestorPromocao = new GestorPromocao(new RepositorioPromocaoEmBDR($bancoDados));

$app->get('/promocoes', function (HttpRequest $request, HttpResponse $response) use ($gestorPromocao) {
    try {
        $restricoes = RestricoesParaBuscaDePromocoes::criarComArray($request->queries() ?? []);
        $promocoes = $gestorPromocao->obterPromocoes($restricoes);

        $response->status(200)->json(['message' => 'Promoções obtidas com sucesso.', 'data' => [
            'promocoes' => $promocoes,
        ]]);
    } catch (DominioException $e) {
        $response->status(400)->json(['message' => 'Erro ao obter promoções.', 'data' => [
            'erros' => $e->erros(),
        ]]);
    } catch (Throwable $e) {
        $response->status(500)->json(['message' => 'Houve um erro interno no servidor.']);
    }
});
