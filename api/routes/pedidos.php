<?php

use phputil\router\HttpRequest;
use phputil\router\HttpResponse;

$gestorPedido = new GestorPedido(
    new UnidadeTrabalhoEmBDR($bancoDados),
    new RepositorioPedidoEmBDR($bancoDados),
    new RepositorioClienteEmBDR($bancoDados),
    new RepositorioPizzaEmBDR($bancoDados),
    new RepositorioProdutoSimplesEmBDR($bancoDados),
    new RepositorioIngredienteEmBDR($bancoDados),
    new RepositorioPromocaoEmBDR($bancoDados),
    new RepositorioConfiguracaoFidelidadeEmBDR($bancoDados),
);

$app->post('/pedidos/descontos', function (HttpRequest $request, HttpResponse $response) use ($gestorPedido) {
    try {
        $pedidoParaCadastro = PedidoParaCadastro::criarComArray((array) $request->body());
        $resumoDescontos = $gestorPedido->obterResumoDescontos($pedidoParaCadastro);

        $response->status(200)->json(['message' => 'Resumo dos descontos obtido com sucesso.', 'data' => [
            'resumoDescontos' => $resumoDescontos,
        ]]);
    } catch (DominioException $e) {
        $response->status(400)->json(['message' => 'Erro ao validar pedido.', 'data' => [
            'erros' => $e->erros(),
        ]]);
    } catch (Throwable $e) {
        $response->status(500)->json(['message' => 'Houve um erro interno no servidor.']);
    }
});

$app->post('/pedidos', function (HttpRequest $request, HttpResponse $response) use ($gestorPedido) {
    try {
        $pedidoParaCadastro = PedidoParaCadastro::criarComArray((array) $request->body());
        $gestorPedido->registrarPedido($pedidoParaCadastro);

        $response->status(201)->json(['message' => 'Pedido registrado com sucesso.']);
    } catch (DominioException $e) {
        $response->status(400)->json(['message' => 'Erro ao registrar pedido.', 'data' => [
            'erros' => $e->erros(),
        ]]);
    } catch (Throwable $e) {
        $response->status(500)->json(['message' => 'Houve um erro interno no servidor.']);
    }
});
