<?php


class SaborEscolhido {
    /**
     * @param IngredienteEscolhido[] $ingredientesEscolhidos
     */
    public function __construct(
        public readonly int $id,
        public readonly Pizza $pizza,
        public readonly array $ingredientesEscolhidos,
    ) {}
}
