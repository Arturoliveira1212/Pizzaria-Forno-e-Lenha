<?php

class Cliente {
    public function __construct(
        public readonly string $id,
        public readonly string $nome,
        public readonly Telefone $telefone,
        public readonly Cpf $cpf,
        public readonly Endereco $endereco,
        public readonly ?BeneficioFidelidade $beneficioFidelidade,
    ) {}

    public function temBeneficioFidelidade(): bool {
        return $this->beneficioFidelidade !== null;
    }

    public function temBeneficioFidelidadeValidoParaUso(): bool {
        return $this->temBeneficioFidelidade() && $this->beneficioFidelidade->estaValidoParaUso();
    }
}
