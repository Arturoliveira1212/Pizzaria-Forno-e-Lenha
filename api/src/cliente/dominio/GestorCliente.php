<?php

class GestorCliente {
    public function __construct(
        private readonly RepositorioCliente $repositorio,
    ) {}

    /**
     * @return Cliente[]
     */
    public function obterClientes(RestricoesParaBuscaClientes $restricoes): array {
        $filtro = new FiltroCliente(
            identificador: trim($restricoes->identificador ?? ''),
            pagina: $restricoes->pagina ?? FiltroCliente::PAGINA_PADRAO,
            tamanhoPagina: $restricoes->tamanhoPagina ?? FiltroCliente::TAMANHO_PAGINA_PADRAO,
        );

        return $this->repositorio->obterClientes($filtro);
    }
}
