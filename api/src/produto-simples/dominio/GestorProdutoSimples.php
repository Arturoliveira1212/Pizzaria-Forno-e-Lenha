<?php

class GestorProdutoSimples {
    public function __construct(
        private readonly RepositorioProdutoSimples $repositorio,
    ) {}

    /**
     * @return ProdutoSimples[]
     */
    public function obterProdutosSimples(RestricoesParaBuscaDeProdutosSimples $restricoes): array {
        $filtro = new FiltroProdutoSimples(
            identificador: trim($restricoes->identificador ?? ''),
            tipoEmString: $restricoes->tipo,
            paginacao: new Paginacao(
                pagina: $restricoes->pagina ?? Paginacao::PAGINA_PADRAO,
                limite: $restricoes->limite ?? Paginacao::LIMITE_PADRAO,
            ),
        );

        return $this->repositorio->obterProdutosSimples($filtro);
    }
}
