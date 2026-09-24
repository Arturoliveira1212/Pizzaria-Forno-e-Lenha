<?php

interface RepositorioPizza {
    /**
     * @return PizzaParaCardapio[]
    */
    public function obterPizzasParaCardapio(): array;

    /**
     * @return Pizza[]
    */
    public function obterPizzas(): array;
}
