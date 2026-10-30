<?php

class SessaoEmMemoria implements Sessao {
    /** @var array<string, mixed> */
    public array $dados = [];

    public bool $destruida = false;

    public function iniciar(): void {}

    public function obter(string $chave): mixed {
        return $this->dados[$chave] ?? null;
    }

    public function definir(string $chave, mixed $valor): void {
        $this->dados[$chave] = $valor;
    }

    public function limpar(): void {
        $this->dados = [];
    }

    public function regenerar(): void {}

    public function destruir(): void {
        $this->dados = [];
        $this->destruida = true;
    }
}
