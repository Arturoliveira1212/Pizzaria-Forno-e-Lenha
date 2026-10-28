<?php

class GestorEntregador {
    public function __construct(
        private readonly RepositorioEntregador $repositorio,
    ) {}

    /**
     * @return Entregador[]
     */
    public function obterEntregadoresDisponiveis(): array {
        return $this->repositorio->obterEntregadoresDisponiveis();
    }
}
