<?php

class GestorCliente {
    public function __construct(
        private readonly RepositorioCliente $repositorio,
    ) {}

    /**
     * @return Cliente[]
     */
    public function obterClientesPorNome(string $busca): array {
        return $this->repositorio->obterClientesPorNome($busca);
    }
}
