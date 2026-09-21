<?php

class ItemSimples extends ItemPedido {
    public function __construct(
        int $id,
        int $quantidade,
        public readonly ProdutoSimples $produtoSimples,
    ) {
        parent::__construct($id, $quantidade);
    }

    public function calcularTotal(): Dinheiro {
        return new Dinheiro(0);
    }
}
