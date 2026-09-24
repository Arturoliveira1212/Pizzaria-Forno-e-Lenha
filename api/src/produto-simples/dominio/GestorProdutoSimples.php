<?php

class GestorProdutoSimples {
    public function __construct(
        private readonly RepositorioProdutoSimples $repositorio,
    ) {}

    /**
     * @return ProdutoSimples[]
     */
    public function obterProdutosSimplesPorTipo(string $tipo): array {
        $tipoProdutoSimples = TipoProdutoSimples::tryFrom($tipo);
        if ($tipoProdutoSimples === null) {
            throw new DominioException("Tipo de produto simples inválido: {$tipo}");
        }

        return $this->repositorio->obterProdutosSimplesPorTipo($tipoProdutoSimples);
    }
}
