<?php

class ProdutoSimples {
    public function __construct(
        public readonly int $id,
        public readonly string $nome,
        public readonly string $descricao,
        public readonly TipoProdutoSimples $tipo,
        public readonly Dinheiro $preco,
        public readonly QuantidadeMedida $estoque,
    ) {}

    public function temEstoqueSuficiente(QuantidadeMedida $quantidadeSolicitada): bool {
        return $quantidadeSolicitada->ehMenorOuIgualA($this->estoque);
    }
}
