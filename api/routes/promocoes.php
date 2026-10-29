<?php

use phputil\router\HttpRequest;
use phputil\router\HttpResponse;
use phputil\router\Router;

return function (Router $app, BancoDados $bancoDados) {

    $app->get('/promocoes', function (HttpRequest $request, HttpResponse $response) use ($bancoDados) {
        try {
            $restricoes = RestricoesParaBuscaDePromocoes::criarComArray($request->queries() ?? []);

            $gestorPromocao = new GestorPromocao(new RepositorioPromocaoEmBDR($bancoDados));
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

};
