<?php

class GestorPizza {
    public function __construct(
        private readonly RepositorioPizza $repositorio,
    ) {}

    /**
     * @return PizzaParaCardapio[]
     */
    public function obterPizzasParaCardapio(): array {
        return $this->repositorio->obterPizzasParaCardapio();
    }

    /**
     * @return Pizza[]
     */
    public function obterPizzas(): array {
        return $this->repositorio->obterPizzas();
    }
}
