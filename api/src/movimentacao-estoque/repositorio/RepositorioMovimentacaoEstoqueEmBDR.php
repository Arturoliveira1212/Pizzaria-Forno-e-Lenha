<?php

class RepositorioMovimentacaoEstoqueEmBDR implements RepositorioMovimentacaoEstoque {
    public function __construct(
        private readonly BancoDados $bancoDados,
    ) {}

    public function registrarMovimentacaoEstoque(Pedido $pedido, MovimentacaoEstoque $movimentacao): void {
        if ($movimentacao instanceof MovimentacaoEstoqueIngrediente) {
            $this->registrarMovimentacaoEstoqueIngrediente(pedido: $pedido, movimentacao: $movimentacao);

            return;
        }

        if ($movimentacao instanceof MovimentacaoEstoqueProdutoSimples) {
            $this->registrarMovimentacaoEstoqueProdutoSimples(pedido: $pedido, movimentacao: $movimentacao);

            return;
        }
    }

    /**
     * @return array{idPedido: int, data: string, tipoOperacaoEstoque: string, quantidade: float, idUnidadeMedida: int}
     */
    private function parametrosComuns(Pedido $pedido, MovimentacaoEstoque $movimentacao): array {
        return [
            'idPedido' => $pedido->id,
            'data' => $movimentacao->data->format('Y-m-d H:i:s'),
            'tipoOperacaoEstoque' => $movimentacao->tipoOperacaoEstoque->value,
            'quantidade' => $movimentacao->quantidadeMedida->valor,
            'idUnidadeMedida' => $movimentacao->quantidadeMedida->unidadeMedida->id,
        ];
    }

    private function registrarMovimentacaoEstoqueIngrediente(Pedido $pedido, MovimentacaoEstoqueIngrediente $movimentacao): void {
        try {
            $sql = 'INSERT INTO movimentacao_estoque_ingrediente (
                    idPedido,
                    idIngrediente,
                    data,
                    tipoOperacaoEstoque,
                    quantidade,
                    idUnidadeMedida
                ) VALUES (
                    :idPedido,
                    :idIngrediente,
                    :data,
                    :tipoOperacaoEstoque,
                    :quantidade,
                    :idUnidadeMedida
                )';

            $this->bancoDados->executar($sql, [
                ...$this->parametrosComuns($pedido, $movimentacao),
                'idIngrediente' => $movimentacao->ingrediente->id,
            ]);
        } catch (Throwable $e) {
            throw new RepositorioException('Erro ao registrar a movimentação de estoque do ingrediente.' . $e->getMessage());
        }
    }

    private function registrarMovimentacaoEstoqueProdutoSimples(Pedido $pedido, MovimentacaoEstoqueProdutoSimples $movimentacao): void {
        try {
            $sql = 'INSERT INTO movimentacao_estoque_produto_simples (
                    idPedido,
                    idProdutoSimples,
                    data,
                    tipoOperacaoEstoque,
                    quantidade,
                    idUnidadeMedida
                ) VALUES (
                    :idPedido,
                    :idProdutoSimples,
                    :data,
                    :tipoOperacaoEstoque,
                    :quantidade,
                    :idUnidadeMedida
                )';

            $this->bancoDados->executar($sql, [
                ...$this->parametrosComuns($pedido, $movimentacao),
                'idProdutoSimples' => $movimentacao->produtoSimples->id,
            ]);
        } catch (Throwable $e) {
            throw new RepositorioException('Erro ao registrar a movimentação de estoque do produto simples.' . $e->getMessage());
        }
    }
}
