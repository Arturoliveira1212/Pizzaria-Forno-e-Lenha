<?php

class IngredienteSubstituto {
    /**
     * @param QuantidadePorTamanho[] $quantidadesPorTamanho
     */
    public function __construct(
        public readonly int $id,
        public readonly Ingrediente $ingrediente,
        public readonly array $quantidadesPorTamanho = [],
    ) {}
}
