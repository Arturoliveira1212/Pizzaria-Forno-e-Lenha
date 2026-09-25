<?php

class RepositorioPromocaoEmBDR implements RepositorioPromocao {
    public function __construct(
        private readonly BancoDados $bancoDados,
    ) {}

    public function obterPromocoes(FiltroPromocao $filtro): array {
        return [];
    }
}
