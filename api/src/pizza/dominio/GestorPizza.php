<?php

class GestorPizza {
    public function __construct(
        private readonly RepositorioPizza $repositorio,
    ) {}

    /**
     * @return Pizza[]
     */
    public function obterPizzas(): array {
        return $this->repositorio->obterPizzas();
    }
}
