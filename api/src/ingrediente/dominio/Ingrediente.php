<?php

class Ingrediente {
    public function __construct(
        public readonly int $id,
        public readonly string $nome,
        public readonly QuantidadeMedida $estoque,
    ) {}
}
