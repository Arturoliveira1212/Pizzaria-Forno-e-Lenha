<?php

class RepositorioPizzaEmBDR implements RepositorioPizza {
    public function __construct(
        private BancoDados $bancoDados,
    ) {}

    public function obterPizzas(): array {
        $resultado = $this->bancoDados->consultar('SELECT * FROM pizzas');

        $pizzas = [new Pizza()];

        return $pizzas;
    }
}
