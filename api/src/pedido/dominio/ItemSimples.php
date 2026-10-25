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

    public function obterConsumo(): QuantidadeMedida {
        $unidadeMedida = $this->produtoSimples->estoque->unidadeMedida;

        return new QuantidadeMedida($this->quantidade, $unidadeMedida);
    }

    public function copiarComQuantidade(int $quantidade): ItemSimples {
        return new ItemSimples(
            id: $this->id,
            quantidade: $quantidade,
            produtoSimples: $this->produtoSimples,
        );
    }
}
