<?php

use phputil\router\HttpRequest;
use phputil\router\HttpResponse;

$gestorPizza = new GestorPizza(new RepositorioPizzaEmBDR($bancoDados));

$app->get('/pizzas', function (HttpRequest $request, HttpResponse $response) use ($gestorPizza) {
    try {
        $cardapio = filter_var($request->param('cardapio'), FILTER_VALIDATE_BOOLEAN);
        $pizzas = $cardapio
            ? $gestorPizza->obterPizzasParaCardapio()
            : $gestorPizza->obterPizzas();

        $response->status(200)->json(['data' => $pizzas, 'message' => 'Pizzas obtidas com sucesso.']);
    } catch (Throwable $e) {
        $response->status(500)->json(['message' => 'Houve um erro interno no servidor.' . $e->getMessage()]);
    }
});
