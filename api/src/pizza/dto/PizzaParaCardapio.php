<?php

class PizzaParaCardapio {
    /**
     * @param PrecoPorTamanhoParaCardapio[] $precosPorTamanho
     */
    public function __construct(
        public readonly int $id,
        public readonly string $nome,
        public readonly string $descricao,
        public readonly array $precosPorTamanho,
    ) {}
}
