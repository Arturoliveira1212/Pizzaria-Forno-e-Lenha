<?php

use phputil\router\HttpRequest;
use phputil\router\HttpResponse;

$gestorPizza = new GestorPizza(new RepositorioPizzaEmBDR($bancoDados));

$app->get('/pizzas', function (HttpRequest $request, HttpResponse $response) use ($gestorPizza) {
    try {
        $restricoes = RestricoesParaBuscaDePizzas::criarComArray($request->queries() ?? []);
        $cardapio = filter_var($request->param('cardapio'), FILTER_VALIDATE_BOOLEAN);

        $pizzas = $cardapio
            ? $gestorPizza->obterPizzasParaCardapio($restricoes)
            : $gestorPizza->obterPizzas($restricoes);

        $response->status(200)->json(['message' => 'Pizzas obtidas com sucesso.', 'data' => [
            'pizzas' => $pizzas,
        ]]);
    } catch (DominioException $e) {
        $response->status(400)->json(['message' => 'Erro ao obter pizzas.', 'data' => [
            'erros' => $e->erros(),
        ]]);
    } catch (Throwable $e) {
        $response->status(500)->json(['message' => 'Houve um erro interno no servidor.']);
    }
});

$app->get('/pizzas/:id', function (HttpRequest $request, HttpResponse $response) use ($gestorPizza) {
    try {
        $id = (int) $request->param('id');
        $pizza = $gestorPizza->obterPizzaPorId($id);

        $response->status(200)->json(['message' => 'Pizza obtida com sucesso.', 'data' => [
            'pizza' => $pizza,
        ]]);
    } catch (NaoEncontradoException $e) {
        $response->status(404)->json(['message' => 'Pizza não encontrada.', 'data']);
    } catch (Throwable $e) {
        $response->status(500)->json(['message' => 'Houve um erro interno no servidor.']);
    }
});
