<?php

class PrecoPorTamanho {
    public function __construct(
        public readonly int $id,
        public readonly TamanhoPizza $tamanhoPizza,
        public readonly Dinheiro $preco,
    ) {}
}
