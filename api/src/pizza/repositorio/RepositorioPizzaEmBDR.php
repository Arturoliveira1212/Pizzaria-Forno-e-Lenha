<?php

class RepositorioPizzaEmBDR implements RepositorioPizza {
    public function __construct(
        private readonly BancoDados $bancoDados,
    ) {}

    public function obterPizzasParaCardapio(): array {
        return [];
    }

    public function obterPizzas(): array {
        return [];
    }
}
