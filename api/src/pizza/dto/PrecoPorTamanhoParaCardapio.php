<?php

class PrecoPorTamanhoParaCardapio {
    public function __construct(
        public readonly int $id,
        public readonly string $tamanhoPizza,
        public readonly float $preco,
    ) {}
}
