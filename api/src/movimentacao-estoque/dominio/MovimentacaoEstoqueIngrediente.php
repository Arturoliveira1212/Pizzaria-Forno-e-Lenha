<?php

class MovimentacaoEstoqueIngrediente extends MovimentacaoEstoque {
    public function __construct(
        int $id,
        DateTime $data,
        TipoMovimentacaoEstoque $tipoMovimentacaoEstoque,
        Quantidade $quantidade,
        public readonly Ingrediente $ingrediente,
    ) {
        parent::__construct($id, $data, $tipoMovimentacaoEstoque, $quantidade);
    }
}
