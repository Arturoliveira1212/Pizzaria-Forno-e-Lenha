<?php

use phputil\router\HttpRequest;
use phputil\router\HttpResponse;

$gestorPromocao = new GestorPromocao(new RepositorioPromocaoEmBDR($bancoDados));

$app->get('/promocoes', function (HttpRequest $request, HttpResponse $response) use ($gestorPromocao) {
    try {
        $somenteAtivasNoMomento = filter_var($request->param('ativasNoMomento'), FILTER_VALIDATE_BOOLEAN);
        $promocoes = $somenteAtivasNoMomento
            ? $gestorPromocao->obterPromocoesAtivasNoMomento()
            : $gestorPromocao->obterPromocoes();

        $response->status(200)->json(['data' => $promocoes, 'message' => 'Promoções obtidas com sucesso.']);
    } catch (Throwable $e) {
        $response->status(500)->json(['message' => 'Houve um erro interno no servidor.' . $e->getMessage()]);
    }
});
