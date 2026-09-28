<?php

interface RepositorioProdutoSimples {
    /**
     * @return ProdutoSimples[]
     */
    public function obterProdutosSimples(FiltroProdutoSimples $filtro): array;

    public function obterProdutoSimplesPorId(int $idProduto): ?ProdutoSimples;
}
