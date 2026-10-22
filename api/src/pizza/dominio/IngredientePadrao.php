<?php

class IngredientePadrao {
    /**
     * @param QuantidadePorTamanho[] $quantidadesPorTamanho
     */
    public function __construct(
        public readonly int $id,
        public readonly Ingrediente $ingrediente,
        public readonly bool $removivel,
        public readonly bool $substituivel,
        public readonly array $quantidadesPorTamanho = [],
    ) {}
}
