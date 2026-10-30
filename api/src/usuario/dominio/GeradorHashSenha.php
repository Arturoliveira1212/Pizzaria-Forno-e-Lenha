<?php

class GeradorHashSenha {
    public function __construct(
        private readonly string $pimenta,
    ) {}

    public function gerar(string $senha): string {
        return password_hash($this->pimenta . $senha, PASSWORD_ARGON2ID);
    }

    public function verificar(string $senha, string $hash): bool {
        return password_verify($this->pimenta . $senha, $hash);
    }
}
