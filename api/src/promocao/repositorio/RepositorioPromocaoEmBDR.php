<?php

class RepositorioPromocaoEmBDR implements RepositorioPromocao {
    public function __construct(
        private readonly BancoDados $bancoDados,
    ) {}

    public function obterPromocoesAtivasNoMomento(): array {
        return [];
    }

    public function obterPromocoes(): array {
        return [];
    }
}
