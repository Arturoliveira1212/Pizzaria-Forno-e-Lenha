<?php

class GestorPromocao {
    public function __construct(
        private readonly RepositorioPromocao $repositorio,
    ) {}

    /**
     * @return Promocao[]
     */
    public function obterPromocoes(RestricoesParaBuscaDePromocoes $restricoes): array {
        $filtro = new FiltroPromocao(
            apenasVigentes: $restricoes->apenasVigentes ?? false,
            paginacao: new Paginacao(
                pagina: $restricoes->pagina ?? Paginacao::PAGINA_PADRAO,
                limite: $restricoes->limite ?? Paginacao::LIMITE_PADRAO,
            ),
        );

        return $this->repositorio->obterPromocoes($filtro);
    }
}
