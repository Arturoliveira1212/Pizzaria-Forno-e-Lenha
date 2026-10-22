<?php

class MovimentacaoEstoqueProdutoSimples extends MovimentacaoEstoque {
    public function __construct(
        int $id,
        DateTime $data,
        TipoMovimentacaoEstoque $tipoMovimentacaoEstoque,
        Quantidade $quantidade,
        public readonly ProdutoSimples $produtoSimples,
    ) {
        parent::__construct($id, $data, $tipoMovimentacaoEstoque, $quantidade);
    }
}
