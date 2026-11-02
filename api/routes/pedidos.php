<?php

use phputil\router\HttpRequest;
use phputil\router\HttpResponse;
use phputil\router\Router;

return function (Router $app, BancoDados $bancoDados) {

    $app->get('/pedidos', function (HttpRequest $request, HttpResponse $response) use ($bancoDados) {
        try {
            $identificador = trim($request->queries()['identificador'] ?? '');
            $idPedido = $identificador === '' ? null : filter_var($identificador, FILTER_VALIDATE_INT);

            if ($identificador !== '' && $idPedido === false) {
                throw new DominioException('O identificador do pedido deve ser um número.');
            }

            $gestorPedido = GestorFactory::criarGestorPedido($bancoDados);
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

    $app->get('/pedidos/:id', function (HttpRequest $request, HttpResponse $response) use ($bancoDados) {
        try {
            $id = (int) $request->param('id');

            $gestorPedido = GestorFactory::criarGestorPedido($bancoDados);
            $detalhesPedido = $gestorPedido->obterDetalhesPedido($id);

            $response->status(200)->json(['message' => 'Pedido obtido com sucesso.', 'data' => [
                'pedido' => $detalhesPedido,
            ]]);
        } catch (NaoEncontradoException $e) {
            $response->status(404)->json(['message' => 'Erro ao obter pedido.', 'data' => [
                'erros' => [$e->getMessage()],
            ]]);
        } catch (Throwable $e) {
            $response->status(500)->json(['message' => 'Houve um erro interno no servidor.']);
        }
    });

    $app->patch('/pedidos/status', function (HttpRequest $request, HttpResponse $response) use ($bancoDados) {
        try {
            $dados = (array) $request->body();
            $idPedido = filter_var($dados['idPedido'] ?? '', FILTER_VALIDATE_INT);
            $status = (string) ($dados['status'] ?? '');
            $motivoCancelamento = (string) ($dados['motivoCancelamento'] ?? '');

            if ($idPedido === false || $status === '') {
                throw new DominioException('ID e status do pedido são obrigatórios.');
            }

            $gestorPedido = GestorFactory::criarGestorPedido($bancoDados);
            $gestorPedido->alterarStatus($idPedido, $status, $motivoCancelamento);
            $response->status(200)->json(['message' => 'Status do pedido alterado com sucesso.']);
        } catch (DominioException $e) {
            $response->status(400)->json(['message' => 'Erro ao alterar status do pedido.', 'data' => [
                'erros' => $e->erros(),
            ]]);
        } catch (NaoEncontradoException $e) {
            $response->status(404)->json(['message' => 'Erro ao alterar status do pedido.', 'data' => [
                'erros' => [$e->getMessage()],
            ]]);
        } catch (Throwable $e) {
            $response->status(500)->json(['message' => 'Houve um erro interno no servidor.']);
        }
    });

    $app->post('/pedidos/saida-entrega', function (HttpRequest $request, HttpResponse $response) use ($bancoDados) {
        try {
            $saidaParaEntregaParaCadastro = SaidaParaEntregaParaCadastro::criarComArray((array) $request->body());

            $gestorPedido = GestorFactory::criarGestorPedido($bancoDados);
            $gestorPedido->registrarSaidaParaEntrega($saidaParaEntregaParaCadastro);

            $response->status(201)->json(['message' => 'Saída para entrega registrada com sucesso.']);
        } catch (DominioException $e) {
            $response->status(400)->json(['message' => 'Erro ao registrar saída para entrega.', 'data' => [
                'erros' => $e->erros(),
            ]]);
        } catch (NaoEncontradoException $e) {
            $response->status(404)->json(['message' => 'Erro ao registrar saída para entrega.', 'data' => [
                'erros' => [$e->getMessage()],
            ]]);
        } catch (Throwable $e) {
            $response->status(500)->json(['message' => 'Houve um erro interno no servidor.']);
        }
    });

    $app->post('/pedidos/valores', function (HttpRequest $request, HttpResponse $response) use ($bancoDados) {
        try {
            $pedidoParaCadastro = PedidoParaCadastro::criarComArray((array) $request->body());

            $gestorPedido = GestorFactory::criarGestorPedido($bancoDados);
            $resumoValores = $gestorPedido->obterResumoValores($pedidoParaCadastro);

            $response->status(200)->json(['message' => 'Resumo dos descontos obtido com sucesso.', 'data' => [
                'resumoValores' => $resumoValores,
            ]]);
        } catch (DominioException $e) {
            $response->status(400)->json(['message' => 'Erro ao validar pedido.', 'data' => [
                'erros' => $e->erros(),
            ]]);
        } catch (Throwable $e) {
            $response->status(500)->json(['message' => 'Houve um erro interno no servidor.']);
        }
    });

    $app->post('/pedidos', function (HttpRequest $request, HttpResponse $response) use ($bancoDados) {
        try {
            $pedidoParaCadastro = PedidoParaCadastro::criarComArray((array) $request->body());

            $gestorPedido = GestorFactory::criarGestorPedido($bancoDados);
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

};
