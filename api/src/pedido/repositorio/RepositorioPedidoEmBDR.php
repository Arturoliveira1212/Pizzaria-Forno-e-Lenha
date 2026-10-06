<?php

class RepositorioPedidoEmBDR implements RepositorioPedido {
    public function __construct(
        private readonly BancoDados $bancoDados,
    ) {}

    public function obterPedidos(?int $idPedido = null): array {
        try {
            $parametros = [];
            $sql = 'SELECT
                pedido.id,
                pedido.data,
                pedido.valorTotal,
                pedido.tipoAtendimento,
                pedido.status,
                cliente.nome AS nomeCliente
                    FROM pedido
                        LEFT JOIN cliente ON cliente.id = pedido.idCliente
                            WHERE 1=1';

            if ($idPedido !== null) {
                $sql .= ' AND pedido.id = :id';
                $parametros['id'] = $idPedido;
            }

            $sql .= ' ORDER BY pedido.id DESC LIMIT 50';

            $resultado = $this->bancoDados->consultar($sql, $parametros);
            if (empty($resultado)) {
                return [];
            }

            $pedidos = [];
            foreach ($resultado as $linha) {
                $tipoAtendimento = TipoAtendimento::from($linha['tipoAtendimento']);
                $status = StatusPedido::from($linha['status']);
                $pedidos[] = new PedidoParaListagem(
                    id: (int) $linha['id'],
                    data: $linha['data'],
                    nomeCliente: $linha['nomeCliente'],
                    tipoAtendimento: $tipoAtendimento->value,
                    status: $status->value,
                    proximosStatus: array_map(
                        fn(StatusPedido $status): string => $status->value,
                        $status->proximos($tipoAtendimento),
                    ),
                    valorTotalCentavos: (int) $linha['valorTotal'],
                );
            }

            return $pedidos;
        } catch (Throwable $e) {
            throw new RepositorioException('Erro ao obter pedidos.');
        }
    }

    public function alterarStatus(int $idPedido, StatusPedido $novoStatus): void {
        try {
            $resultado = $this->bancoDados->consultar(
                'SELECT status, tipoAtendimento FROM pedido WHERE id = :id',
                ['id' => $idPedido],
            );
            if (empty($resultado)) {
                throw new DominioException("Pedido (ID {$idPedido}) não encontrado.");
            }

            $linha = $resultado[0];
            $statusAtual = StatusPedido::from($linha['status']);
            $tipoAtendimento = TipoAtendimento::from($linha['tipoAtendimento']);

            if (!in_array($novoStatus, $statusAtual->proximos($tipoAtendimento), true)) {
                throw new DominioException("Não é possível alterar o pedido de {$statusAtual->value} para {$novoStatus->value}.");
            }

            $this->bancoDados->executar(
                'UPDATE pedido SET status = :status WHERE id = :id',
                ['status' => $novoStatus->value, 'id' => $idPedido],
            );
        } catch (DominioException $e) {
            throw $e;
        } catch (Throwable $e) {
            throw new RepositorioException('Erro ao alterar status do pedido.');
        }
    }

    public function registrarPedido(Pedido $pedido): void {
        try {
            $idPedido = $this->salvarPedido($pedido);
            $this->salvarItensPedido($idPedido, $pedido);
            $this->salvarDescontos($idPedido, $pedido);
        } catch (Throwable $e) {
            throw new RepositorioException('Erro ao registrar pedido.');
        }
    }

    private function salvarPedido(Pedido $pedido): int {
        $sql = 'INSERT INTO pedido (
                data,
                valorTotal,
                taxaEntrega,
                status,
                tipoAtendimento,
                idCliente
            ) VALUES (
                :data,
                :valorTotal,
                :taxaEntrega,
                :status,
                :tipoAtendimento,
                :idCliente
            )';

        $this->bancoDados->executar($sql, [
            'data' => $pedido->data->format('Y-m-d H:i:s'),
            'valorTotal' => $pedido->calcularValorTotal()->centavos,
            'taxaEntrega' => $pedido->calcularValorEntrega()->centavos,
            'status' => $pedido->status->value,
            'tipoAtendimento' => $pedido->tipoAtendimento->value,
            'idCliente' => $pedido->cliente?->id,
        ]);

        return (int) $this->bancoDados->ultimoIdInserido();
    }

    private function salvarDescontos(int $idPedido, Pedido $pedido): void {
        foreach ($pedido->descontosPromocao as $desconto) {
            $sql = 'INSERT INTO desconto_promocao (
                    pedidoId,
                    valorDesconto,
                    promocaoId
                ) VALUES (
                    :pedidoId,
                    :valorDesconto,
                    :promocaoId
                )';

            $this->bancoDados->executar($sql, [
                'pedidoId' => $idPedido,
                'valorDesconto' => $desconto->valorDesconto->centavos,
                'promocaoId' => $desconto->promocao->id,
            ]);
        }

        if ($pedido->descontoFidelidade instanceof DescontoFidelidade) {
            $sql = 'INSERT INTO desconto_fidelidade (
                    pedidoId,
                    valorDesconto,
                    beneficioFidelidadeId
                ) VALUES (
                    :pedidoId,
                    :valorDesconto,
                    :beneficioFidelidadeId
                )';

            $this->bancoDados->executar($sql, [
                'pedidoId' => $idPedido,
                'valorDesconto' => $pedido->descontoFidelidade->valorDesconto->centavos,
                'beneficioFidelidadeId' => $pedido->descontoFidelidade->beneficioFidelidade->id,
            ]);
        }
    }

    private function salvarItensPedido(int $idPedido, Pedido $pedido): void {
        foreach ($pedido->itensPedido as $itemPedido) {
            if ($itemPedido instanceof ItemPizza) {
                $this->salvarItemPizza($idPedido, $itemPedido);
                continue;
            }

            if ($itemPedido instanceof ItemSimples) {
                $this->salvarItemSimples($idPedido, $itemPedido);
            }
        }
    }

    private function salvarItemSimples(int $idPedido, ItemSimples $itemSimples): void {
        $sql = 'INSERT INTO item_pedido_simples (
                pedidoId,
                quantidade,
                precoPraticado,
                produtoSimplesId
            ) VALUES (
                :pedidoId,
                :quantidade,
                :precoPraticado,
                :produtoSimplesId
            )';

        $this->bancoDados->executar($sql, [
            'pedidoId' => $idPedido,
            'quantidade' => $itemSimples->quantidade,
            'precoPraticado' => $itemSimples->precoPraticado->centavos,
            'produtoSimplesId' => $itemSimples->produtoSimples->id,
        ]);
    }

    private function salvarItemPizza(int $idPedido, ItemPizza $itemPizza): void {
        $sql = 'INSERT INTO item_pedido_pizza (
                pedidoId,
                quantidade,
                precoPraticado,
                tamanho
            ) VALUES (
                :pedidoId,
                :quantidade,
                :precoPraticado,
                :tamanho
            )';

        $this->bancoDados->executar($sql, [
            'pedidoId' => $idPedido,
            'quantidade' => $itemPizza->quantidade,
            'precoPraticado' => $itemPizza->precoPraticado->centavos,
            'tamanho' => $itemPizza->tamanhoPizza->value,
        ]);

        $idItemPizza = (int) $this->bancoDados->ultimoIdInserido();
        foreach ($itemPizza->saboresEscolhidos as $saborEscolhido) {
            $this->salvarSaborEscolhido($idItemPizza, $saborEscolhido);
        }
    }

    private function salvarSaborEscolhido(int $idItemPizza, SaborEscolhido $saborEscolhido): void {
        $sql = 'INSERT INTO sabor_escolhido (
                itemPedidoPizzaId,
                pizzaId
            ) VALUES (
                :itemPedidoPizzaId,
                :pizzaId
            )';

        $this->bancoDados->executar($sql, [
            'itemPedidoPizzaId' => $idItemPizza,
            'pizzaId' => $saborEscolhido->pizza->id,
        ]);

        $idSaborEscolhido = (int) $this->bancoDados->ultimoIdInserido();
        foreach ($saborEscolhido->ingredientesEscolhidos as $ingredienteEscolhido) {
            $this->salvarIngredienteEscolhido($idSaborEscolhido, $ingredienteEscolhido);
        }
    }

    private function salvarIngredienteEscolhido(int $idSaborEscolhido, IngredienteEscolhido $ingredienteEscolhido): void {
        $sql = 'INSERT INTO ingrediente_escolhido (
                saborEscolhidoId,
                ingredienteId,
                custo,
                papel
            ) VALUES (
                :saborEscolhidoId,
                :ingredienteId,
                :custo,
                :papel
            )';

        $this->bancoDados->executar($sql, [
            'saborEscolhidoId' => $idSaborEscolhido,
            'ingredienteId' => $ingredienteEscolhido->ingrediente->id,
            'custo' => $ingredienteEscolhido->custoAdicional->centavos,
            'papel' => $ingredienteEscolhido->papel->value,
        ]);
    }
}
