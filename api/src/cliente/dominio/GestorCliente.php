<?php

class GestorCliente {
    public function __construct(
        private readonly RepositorioCliente $repositorio,
    ) {}

    /**
     * @return Cliente[]
     */
    public function obterClientes(RestricoesParaBuscaDeClientes $restricoes): array {
        $filtro = new FiltroCliente(
            identificador: trim($restricoes->identificador ?? ''),
            paginacao: new Paginacao(
                pagina: $restricoes->pagina ?? Paginacao::PAGINA_PADRAO,
                limite: $restricoes->limite ?? FiltroCliente::LIMITE_PADRAO,
            ),
        );

        return $this->repositorio->obterClientes($filtro);
    }
}
