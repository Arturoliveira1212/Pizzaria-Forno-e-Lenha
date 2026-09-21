<?php

class Entregador {
    public function __construct(
        public readonly int $id,
        public readonly string $nome,
        public readonly Cpf $cpf,
        public readonly Telefone $telefone,
        public readonly bool $disponivel,
    ) {}
}
