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
    new RepositorioMovimentacaoEstoqueEmBDR($bancoDados),
    new RepositorioEntregadorEmBDR($bancoDados),
);

$app->get('/pedidos', function (HttpRequest $request, HttpResponse $response) use ($gestorPedido) {
    try {
        $identificador = trim($request->queries()['identificador'] ?? '');
        $idPedido = $identificador === '' ? null : filter_var($identificador, FILTER_VALIDATE_INT);

        if ($identificador !== '' && $idPedido === false) {
            throw new DominioException('O identificador do pedido deve ser um número.');
        }

        $pedidos = $gestorPedido->obterPedidosParaListagem($idPedido);

        $response->status(200)->json(['message' => 'Pedidos obtidos com sucesso.', 'data' => [
            'pedidos' => $pedidos,
        ]]);
    } catch (DominioException $e) {
        $response->status(400)->json(['message' => 'Erro ao obter pedidos.', 'data' => [
            'erros' => $e->erros(),
        ]]);
    } catch (Throwable $e) {
        $response->status(500)->json(['message' => 'Houve um erro interno no servidor.']);
    }
});

$app->patch('/pedidos/status', function (HttpRequest $request, HttpResponse $response) use ($gestorPedido) {
    try {
        $dados = (array) $request->body();
        $idPedido = filter_var($dados['idPedido'] ?? '', FILTER_VALIDATE_INT);
        $status = (string) ($dados['status'] ?? '');

        if ($idPedido === false || $status === '') {
            throw new DominioException('ID e status do pedido são obrigatórios.');
        }

        $gestorPedido->alterarStatus($idPedido, $status);
        $response->status(200)->json(['message' => 'Status do pedido alterado com sucesso.']);
    } catch (DominioException $e) {
        $response->status(400)->json(['message' => 'Erro ao alterar status do pedido.', 'data' => [
            'erros' => $e->erros(),
        ]]);
    } catch (Throwable $e) {
        $response->status(500)->json(['message' => 'Houve um erro interno no servidor.']);
    }
});

$app->post('/pedidos/valores', function (HttpRequest $request, HttpResponse $response) use ($gestorPedido) {
    try {
        $pedidoParaCadastro = PedidoParaCadastro::criarComArray((array) $request->body());
        $resumoValores = $gestorPedido->obterResumoValores($pedidoParaCadastro);

        $response->status(200)->json(['message' => 'Resumo dos descontos obtido com sucesso.', 'data' => [
            'resumoValores' => $resumoValores,
        ]]);
    } catch (DominioException $e) {
        $response->status(400)->json(['message' => 'Erro ao validar pedido.', 'data' => [
            'erros' => $e->erros(),
        ]]);
    } catch (Throwable $e) {
        $response->status(500)->json(['message' => 'Houve um erro interno no servidor.' . $e->getMessage()]);
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
