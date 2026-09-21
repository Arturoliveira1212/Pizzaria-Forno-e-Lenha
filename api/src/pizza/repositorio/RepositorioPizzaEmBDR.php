<?php

class RepositorioPizzaEmBDR implements RepositorioPizza {
    public function __construct(
        private readonly BancoDados $bancoDados,
    ) {}

    public function obterPizzas(): array {
        $resultado = $this->bancoDados->consultar('SELECT * FROM pizzas');

        $pizzas = [];

        return $pizzas;
    }
}
