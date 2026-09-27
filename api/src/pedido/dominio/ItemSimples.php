<?php

class ItemSimples extends ItemPedido {
    public function __construct(
        int $id,
        int $quantidade,
        public readonly ProdutoSimples $produtoSimples,
        ?Dinheiro $precoPraticado = null,
    ) {
        parent::__construct($id, $quantidade, $precoPraticado);
    }

    protected function validarCamposEspecificos(array &$erros): void {}

    protected function calcularPrecoUnitario(): Dinheiro {
        return $this->produtoSimples->preco;
    }
}
