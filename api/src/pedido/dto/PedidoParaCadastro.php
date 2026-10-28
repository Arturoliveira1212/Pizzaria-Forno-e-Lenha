<?php

class PedidoParaCadastro {
    /**
     * @param ItemPizzaParaCadastro[]|ItemSimplesParaCadastro[] $itensPedido
     */
    public function __construct(
        public readonly string $tipoAtendimento,
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
            tipoAtendimento: $dados['tipoAtendimento'] ?? '',
            idCliente: $dados['idCliente'] ?? '',
            itensPedido: $itensPedido,
        );
    }
}
