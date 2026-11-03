<?php

use phputil\router\HttpRequest;
use phputil\router\HttpResponse;
use phputil\router\Router;

return function (Router $app, BancoDados $bancoDados) {

    $app->get('/relatorios/pedidos-por-dia', function (HttpRequest $request, HttpResponse $response) use ($bancoDados) {
        try {
            $periodoParaRelatorio = PeriodoParaRelatorio::criarComArray($request->queries() ?? []);

            $gestorRelatorio = GestorFactory::criarGestorRelatorio($bancoDados);
            $totaisPorDia = $gestorRelatorio->obterTotalPedidosPorDia($periodoParaRelatorio);

            $response->status(200)->json(['message' => 'Relatório de pedidos por dia obtido com sucesso.', 'data' => [
                'totaisPorDia' => $totaisPorDia,
            ]]);
        } catch (DominioException $e) {
            $response->status(400)->json(['message' => 'Erro ao obter relatório de pedidos por dia.', 'data' => [
                'erros' => $e->erros(),
            ]]);
        } catch (AutenticacaoException $e) {
            $response->status(401)->json(['message' => 'Não autenticado.', 'data' => [
                'erros' => [$e->getMessage()],
            ]]);
        } catch (SemAcessoException $e) {
            $response->status(403)->json(['message' => 'Acesso negado.', 'data' => [
                'erros' => [$e->getMessage()],
            ]]);
        } catch (Throwable $e) {
            $response->status(500)->json(['message' => 'Houve um erro interno no servidor.']);
        }
    });

    $app->get('/relatorios/distribuicao-itens', function (HttpRequest $request, HttpResponse $response) use ($bancoDados) {
        try {
            $periodoParaRelatorio = PeriodoParaRelatorio::criarComArray($request->queries() ?? []);

            $gestorRelatorio = GestorFactory::criarGestorRelatorio($bancoDados);
            $distribuicaoItens = $gestorRelatorio->obterDistribuicaoItens($periodoParaRelatorio);

            $response->status(200)->json(['message' => 'Relatório de distribuição de itens obtido com sucesso.', 'data' => [
                'itens' => $distribuicaoItens,
            ]]);
        } catch (DominioException $e) {
            $response->status(400)->json(['message' => 'Erro ao obter relatório de distribuição de itens.', 'data' => [
                'erros' => $e->erros(),
            ]]);
        } catch (AutenticacaoException $e) {
            $response->status(401)->json(['message' => 'Não autenticado.', 'data' => [
                'erros' => [$e->getMessage()],
            ]]);
        } catch (SemAcessoException $e) {
            $response->status(403)->json(['message' => 'Acesso negado.', 'data' => [
                'erros' => [$e->getMessage()],
            ]]);
        } catch (Throwable $e) {
            $response->status(500)->json(['message' => 'Houve um erro interno no servidor.']);
        }
    });

};
