<?php

class DominioException extends RuntimeException {
    public readonly array $erros;

    public function __construct(string $mensagem, array $erros = []) {
        parent::__construct($mensagem);
        $this->erros = $erros;
    }
}
