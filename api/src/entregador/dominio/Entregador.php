<?php

class Entregador {
    public function __construct(
        public readonly int $id,
        public readonly string $nome,
        public readonly Telefone $telefone,
        public readonly bool $disponivel,
    ) {}
}
