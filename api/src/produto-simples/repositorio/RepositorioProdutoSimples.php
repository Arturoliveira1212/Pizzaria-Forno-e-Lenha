<?php

interface RepositorioProdutoSimples {
    /**
     * @return ProdutoSimples[]
     */
    public function obterProdutosSimplesPorTipo(TipoProdutoSimples $tipoProdutoSimples): array;
}
