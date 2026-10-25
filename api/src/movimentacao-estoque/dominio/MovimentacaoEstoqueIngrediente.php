<?php

class MovimentacaoEstoqueIngrediente extends MovimentacaoEstoque {
    public function __construct(
        int $id,
        DateTime $data,
        TipoOperacaoEstoque $tipoOperacaoEstoque,
        QuantidadeMedida $quantidade,
        public readonly Ingrediente $ingrediente,
    ) {
        parent::__construct($id, $data, $tipoOperacaoEstoque, $quantidade);
    }
}
