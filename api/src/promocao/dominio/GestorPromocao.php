<?php

class GestorPromocao {
    public function __construct(
        private readonly RepositorioPromocao $repositorio,
    ) {}

    /**
     * @return Promocao[]
     */
    public function obterPromocoesAtivasNoMomento(): array {
        return $this->repositorio->obterPromocoesAtivasNoMomento();
    }

    /**
     * @return Promocao[]
     */
    public function obterPromocoes(): array {
        return $this->repositorio->obterPromocoes();
    }
}
