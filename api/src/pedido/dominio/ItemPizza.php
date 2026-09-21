<?php

class ItemPizza extends ItemPedido {
    /**
     * @param SaborEscolhido[] $saboresEscolhidos
     */
    public function __construct(
        int $id,
        int $quantidade,
        public readonly array $saboresEscolhidos,
        public readonly TamanhoPizza $tamanhoPizza,
    ) {
        parent::__construct($id, $quantidade);
    }

    public function calcularTotal(): Dinheiro {
        return new Dinheiro(0);
    }
}
