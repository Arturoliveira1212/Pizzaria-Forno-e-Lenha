<?php

class RepositorioProdutoSimplesEmBDR implements RepositorioProdutoSimples {
    public function __construct(
        private readonly BancoDados $bancoDados,
    ) {}

    public function obterProdutosSimplesPorTipo(TipoProdutoSimples $tipoProdutoSimples): array {
        return [];
    }
}
