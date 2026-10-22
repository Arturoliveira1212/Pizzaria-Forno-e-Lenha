<?php

class Usuario {
    public function __construct(
        public readonly int $id,
        public readonly string $nome,
        public readonly Cpf $cpf,
        public readonly string $senha,
        public readonly PapelUsuario $papel,
    ) {}
}
