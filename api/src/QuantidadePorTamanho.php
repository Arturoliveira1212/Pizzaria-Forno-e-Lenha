<?php

class QuantidadePorTamanho {
    public function __construct(
        public readonly int $id,
        public readonly TamanhoPizza $tamanhoPizza,
        public readonly QuantidadeMedida $quantidadeMedida,
    ) {}
}
