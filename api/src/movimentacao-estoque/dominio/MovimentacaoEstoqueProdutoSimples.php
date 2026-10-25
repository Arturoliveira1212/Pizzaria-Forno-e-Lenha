<?php

class MovimentacaoEstoqueProdutoSimples extends MovimentacaoEstoque {
    public function __construct(
        int $id,
        DateTime $data,
        TipoOperacaoEstoque $tipoOperacaoEstoque,
        QuantidadeMedida $quantidade,
        public readonly ProdutoSimples $produtoSimples,
    ) {
        parent::__construct($id, $data, $tipoOperacaoEstoque, $quantidade);
    }
}
