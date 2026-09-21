<?php

class Pizza {
    /**
     * @param IngredientePadrao[] $ingredientesPadrao
     * @param Ingrediente[] $ingredientesSubstitutos
     * @param IngredienteAdicional[] $adicionais
     * @param PrecoPorTamanho[] $precos
     */
    public function __construct(
        public readonly int $id,
        public readonly string $nome,
        public readonly string $descricao,
        public readonly array $ingredientesPadrao,
        public readonly array $ingredientesSubstitutos,
        public readonly array $adicionais,
        public readonly array $precos,
    ) {}

}
