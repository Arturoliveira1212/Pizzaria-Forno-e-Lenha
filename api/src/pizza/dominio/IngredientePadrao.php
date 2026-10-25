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
        public readonly array $quantidadesPorTamanho,
    ) {}

    public function ehFixo(): bool {
        return !$this->removivel && !$this->substituivel;
    }

    public function obterQuantidadePorTamanho(TamanhoPizza $tamanhoPizza): ?QuantidadePorTamanho {
        foreach ($this->quantidadesPorTamanho as $quantidadePorTamanho) {
            if ($quantidadePorTamanho->tamanhoPizza === $tamanhoPizza) {
                return $quantidadePorTamanho;
            }
        }

        return null;
    }
}
