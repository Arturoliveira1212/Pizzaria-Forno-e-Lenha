<?php

class PizzaParaCardapio {
    /**
     * @param PrecoPorTamanho[] $precos
     */
    public function __construct(
        public readonly int $id,
        public readonly string $nome,
        public readonly string $descricao,
        public readonly array $precos,
    ) {}
}
