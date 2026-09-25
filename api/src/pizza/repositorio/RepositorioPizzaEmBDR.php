<?php

class RepositorioPizzaEmBDR implements RepositorioPizza {
    public function __construct(
        private readonly BancoDados $bancoDados,
    ) {}

    public function obterPizzasParaCardapio(FiltroPizza $filtro): array {
        return [];
    }

    public function obterPizzas(FiltroPizza $filtro): array {
        return [];
    }
}
