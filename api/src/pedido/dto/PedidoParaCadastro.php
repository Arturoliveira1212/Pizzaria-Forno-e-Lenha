<?php

class PedidoParaCadastro {
    /**
     * @param ItemPizzaParaCadastro[]|ItemSimplesParaCadastro[] $itensPedido
     */
    public function __construct(
        public readonly string $tipo,
        public readonly string $idCliente,
        public readonly array $itensPedido,
    ) {}

    /**
     * @param array<string, mixed> $dados
     */
    public static function criarComArray(array $dados): PedidoParaCadastro {
        $itensPedido = array_map(function (StdClass $itemPedido): ItemPizzaParaCadastro|ItemSimplesParaCadastro {
            $itemPedidoEmArray = (array) $itemPedido;
            if (isset($itemPedidoEmArray['idProduto'])) {
                return ItemSimplesParaCadastro::criarComArray($itemPedidoEmArray);
            }

            return ItemPizzaParaCadastro::criarComArray($itemPedidoEmArray);
        }, $dados['itensPedido'] ?? []);

        return new self(
            tipo: $dados['tipo'] ?? '',
            idCliente: $dados['idCliente'] ?? '',
            itensPedido: $itensPedido,
        );
    }

    /**
     * @return array{0: ItemPizzaParaCadastro[], 1: ItemSimplesParaCadastro[]}
     */
    public function obterItensPorTipo(): array {
        $itensPizza = [];
        $itensSimples = [];

        foreach ($this->itensPedido as $itemPedido) {
            if ($itemPedido instanceof ItemPizzaParaCadastro) {
                $itensPizza[] = $itemPedido;
            } elseif ($itemPedido instanceof ItemSimplesParaCadastro) {
                $itensSimples[] = $itemPedido;
            }
        }

        return [$itensPizza, $itensSimples];
    }
}
