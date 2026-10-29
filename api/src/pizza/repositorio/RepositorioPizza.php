<?php

interface RepositorioPizza {
    /**
     * @return PizzaParaCardapio[]
    */
    public function obterPizzasParaCardapio(FiltroPizza $filtro): array;

    /**
     * @return Pizza[]
    */
    public function obterPizzas(FiltroPizza $filtro): array;

    public function obterPizzaPorId(int $idPizza): ?Pizza;

    /**
     * @param int[] $idsPizzas
     * @return array<int, Pizza>
     */
    public function obterPizzasPorIds(array $idsPizzas): array;
}
