<?php

class IngredienteSubstituto {
    /**
     * @param QuantidadePorTamanho[] $quantidadesPorTamanho
     */
    public function __construct(
        public readonly int $id,
        public readonly Ingrediente $ingrediente,
        public readonly array $quantidadesPorTamanho,
    ) {}

    public function obterQuantidadePorTamanho(TamanhoPizza $tamanhoPizza): ?QuantidadePorTamanho {
        foreach ($this->quantidadesPorTamanho as $quantidadePorTamanho) {
            if ($quantidadePorTamanho->tamanhoPizza === $tamanhoPizza) {
                return $quantidadePorTamanho;
            }
        }

        return null;
    }
}
