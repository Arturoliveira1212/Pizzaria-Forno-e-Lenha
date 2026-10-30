<?php

class DetalhesItemPedido {
    /**
     * @param DetalhesSaborEscolhido[] $sabores
     */
    public function __construct(
        public readonly string $descricao,
        public readonly int $quantidade,
        public readonly Dinheiro $precoUnitario,
        public readonly Dinheiro $precoTotal,
        public readonly array $sabores = [],
    ) {}

    public static function criarComEntidade(ItemPedido $itemPedido): DetalhesItemPedido {
        if ($itemPedido instanceof ItemPizza) {
            return new self(
                descricao: $itemPedido->obterDescricaoSabores(),
                quantidade: $itemPedido->quantidade,
                precoUnitario: $itemPedido->precoPraticado,
                precoTotal: $itemPedido->calcularPrecoTotal(),
                sabores: array_map(
                    fn(SaborEscolhido $saborEscolhido): DetalhesSaborEscolhido => DetalhesSaborEscolhido::criarComEntidade($saborEscolhido),
                    $itemPedido->saboresEscolhidos,
                ),
            );
        }

        /** @var ItemSimples $itemPedido */
        return new self(
            descricao: $itemPedido->produtoSimples->nome,
            quantidade: $itemPedido->quantidade,
            precoUnitario: $itemPedido->precoPraticado,
            precoTotal: $itemPedido->calcularPrecoTotal(),
        );
    }
}
