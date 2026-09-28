<?php

class PedidoParaCadastro {
    /**
     * @param ItemPizzaParaCadastro[] $itensPizza
     * @param ItemSimplesParaCadastro[] $itensSimples
     */
    public function __construct(
        public readonly string $tipo,
        public readonly string $idCliente,
        public readonly array $itensPizza,
        public readonly array $itensSimples,
    ) {}

    /**
     * @param array<string, mixed> $dados
     */
    public static function criarComArray(array $dados): PedidoParaCadastro {
        $itensPizza = array_map(function (StdClass $itemPizza): ItemPizzaParaCadastro {
            return ItemPizzaParaCadastro::criarComArray((array) $itemPizza);
        }, $dados['itensPizza'] ?? []);

        $itensSimples = array_map(function (StdClass $itemSimples): ItemSimplesParaCadastro {
            return ItemSimplesParaCadastro::criarComArray((array) $itemSimples);
        }, $dados['itensSimples'] ?? []);

        return new self(
            tipo: $dados['tipo'] ?? '',
            idCliente: $dados['idCliente'] ?? '',
            itensPizza: $itensPizza,
            itensSimples: $itensSimples,
        );
    }
}
