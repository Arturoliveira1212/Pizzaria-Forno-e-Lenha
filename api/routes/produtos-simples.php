<?php

use phputil\router\HttpRequest;
use phputil\router\HttpResponse;

$gestorProdutoSimples = new GestorProdutoSimples(new RepositorioProdutoSimplesEmBDR($bancoDados));

$app->get('/produtos-simples', function (HttpRequest $request, HttpResponse $response) use ($gestorProdutoSimples) {
    try {
        $tipo = $request->param('tipo') ?? '';
        $produtosSimples = $gestorProdutoSimples->obterProdutosSimplesPorTipo($tipo);

        $response->status(200)->json(['data' => $produtosSimples, 'message' => 'Produtos simples obtidos com sucesso.']);
    } catch (DominioException $e) {
        $response->status(400)->json(['message' => $e->getMessage()]);
    } catch (Throwable $e) {
        $response->status(500)->json(['message' => 'Houve um erro interno no servidor.' . $e->getMessage()]);
    }
});
