<?php

class Cliente {
    public function __construct(
        public readonly string $id,
        public readonly string $nome,
        public readonly Cpf $cpf,
        public readonly Telefone $telefone,
        public readonly ?BeneficioFidelidade $beneficioFidelidade,
    ) {}
}
