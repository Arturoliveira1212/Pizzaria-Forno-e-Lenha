<?php

class RepositorioClienteEmBDR implements RepositorioCliente {
    public function __construct(
        private readonly BancoDados $bancoDados,
    ) {}

    public function obterClientes(FiltroCliente $filtro): array {
        return [];
    }
}
