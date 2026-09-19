<?php

class GestorPizza {
    public function __construct(
        private RepositorioPizza $repositorio,
    ) {}

    /**
     * @return array<Pizza>
     */
    public function obterPizzas(): array {
        return $this->repositorio->obterPizzas();
    }
}
