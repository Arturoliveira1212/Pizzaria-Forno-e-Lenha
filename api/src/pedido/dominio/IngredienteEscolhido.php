<?php

class IngredienteEscolhido {
    public function __construct(
        public readonly int $id,
        public readonly Ingrediente $ingrediente,
        public readonly PapelIngrediente $papel,
        public readonly Dinheiro $custo,
    ) {}
}
