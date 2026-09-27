<?php

use phputil\router\HttpRequest;
use phputil\router\HttpResponse;

$gestorProdutoSimples = new GestorProdutoSimples(new RepositorioProdutoSimplesEmBDR($bancoDados));

$app->get('/produtos-simples', function (HttpRequest $request, HttpResponse $response) use ($gestorProdutoSimples) {
    try {
        $restricoes = RestricoesParaBuscaDeProdutosSimples::criarComArray($request->queries() ?? []);
        $produtosSimples = $gestorProdutoSimples->obterProdutosSimples($restricoes);

        $response->status(200)->json(['data' => $produtosSimples, 'message' => 'Produtos simples obtidos com sucesso.']);
    } catch (DominioException $e) {
        $response->status(400)->json(['message' => 'Erro ao obter produtos simples.', 'erros' => $e->erros()]);
    } catch (Throwable $e) {
        $response->status(500)->json(['message' => 'Houve um erro interno no servidor.' . $e->getMessage()]);
    }
});
