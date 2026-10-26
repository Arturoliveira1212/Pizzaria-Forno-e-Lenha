<?php

class PizzaParaCardapio {
    /**
     * @param PrecoPorTamanho[] $precosPorTamanho
     * @param IngredientePadrao[] $ingredientesPadrao
     */
    public function __construct(
        public readonly int $id,
        public readonly string $nome,
        public readonly string $descricao,
        public readonly array $precosPorTamanho,
        public readonly array $ingredientesPadrao,
    ) {}
}
