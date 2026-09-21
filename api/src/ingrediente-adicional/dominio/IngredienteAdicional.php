<?php

class IngredienteAdicional {
    public function __construct(
        public readonly int $id,
        public readonly Ingrediente $ingrediente,
        public readonly float $preco,
    ) {}
}
