<?php

interface Sessao {
    public function iniciar(): void;

    public function obter(string $chave): mixed;

    public function definir(string $chave, mixed $valor): void;

    public function limpar(): void;

    public function regenerar(): void;

    public function destruir(): void;
}
