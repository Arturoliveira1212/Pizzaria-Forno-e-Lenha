<?php

class RepositorioRelatorioEmBDR implements RepositorioRelatorio {
    public function __construct(
        private readonly BancoDados $bancoDados,
    ) {}

    public function obterTotalPedidosPorDia(PeriodoRelatorio $periodo): array {
        try {
            $sql = "SELECT
                DATE(pedido.data) AS data,
                SUM(pedido.valorTotal) AS valorTotal
                    FROM pedido
                    WHERE pedido.status <> 'CANCELADO'
                        AND pedido.data BETWEEN :dataInicio AND :dataFim
                    GROUP BY DATE(pedido.data)
                    ORDER BY DATE(pedido.data)";

            $resultado = $this->bancoDados->consultar($sql, $this->obterParametrosPeriodo($periodo));

            $totais = [];
            foreach ($resultado as $linha) {
                $totais[] = new TotalPedidosDia(
                    data: $linha['data'],
                    valorTotal: new Dinheiro((int) $linha['valorTotal']),
                );
            }

            return $totais;
        } catch (Throwable $e) {
            throw new RepositorioException('Erro ao obter total de pedidos por dia.');
        }
    }

    public function obterValorVendidoPorItem(PeriodoRelatorio $periodo): array {
        try {
            $sql = "SELECT
                produto_simples.nome AS nome,
                SUM(item_pedido_simples.precoPraticado * item_pedido_simples.quantidade) AS valor
                    FROM item_pedido_simples
                        INNER JOIN pedido ON pedido.id = item_pedido_simples.idPedido
                        INNER JOIN produto_simples ON produto_simples.id = item_pedido_simples.idProdutoSimples
                    WHERE pedido.status <> 'CANCELADO'
                        AND pedido.data BETWEEN :dataInicio AND :dataFim
                    GROUP BY produto_simples.id, produto_simples.nome
                UNION ALL
                SELECT
                CONCAT('Pizza ', pizza.nome) AS nome,
                SUM(item_pedido_pizza.precoPraticado * item_pedido_pizza.quantidade / sabores.quantidade) AS valor
                    FROM item_pedido_pizza
                        INNER JOIN pedido ON pedido.id = item_pedido_pizza.idPedido
                        INNER JOIN sabor_escolhido ON sabor_escolhido.idItemPedidoPizza = item_pedido_pizza.id
                        INNER JOIN pizza ON pizza.id = sabor_escolhido.idPizza
                        INNER JOIN (
                            SELECT idItemPedidoPizza, COUNT(*) AS quantidade
                                FROM sabor_escolhido
                                GROUP BY idItemPedidoPizza
                        ) AS sabores ON sabores.idItemPedidoPizza = item_pedido_pizza.id
                    WHERE pedido.status <> 'CANCELADO'
                        AND pedido.data BETWEEN :dataInicioPizza AND :dataFimPizza
                    GROUP BY pizza.id, pizza.nome
                ORDER BY valor DESC";

            $parametros = $this->obterParametrosPeriodo($periodo);
            $resultado = $this->bancoDados->consultar($sql, $parametros + [
                'dataInicioPizza' => $parametros['dataInicio'],
                'dataFimPizza' => $parametros['dataFim'],
            ]);

            $itens = [];
            foreach ($resultado as $linha) {
                $itens[] = new ValorVendidoItem(
                    nome: $linha['nome'],
                    valor: new Dinheiro((int) round((float) $linha['valor'])),
                );
            }

            return $itens;
        } catch (Throwable $e) {
            throw new RepositorioException('Erro ao obter valor vendido por item.');
        }
    }

    /**
     * @return array{dataInicio: string, dataFim: string}
     */
    private function obterParametrosPeriodo(PeriodoRelatorio $periodo): array {
        return [
            'dataInicio' => $periodo->dataInicio->format('Y-m-d 00:00:00'),
            'dataFim' => $periodo->dataFim->format('Y-m-d 23:59:59'),
        ];
    }
}
