<?php

class RegistroPedidoException extends RuntimeException {
    public function __construct(
        public readonly array $erros,
        public readonly array $itensPedidoAtualizados = [],
        string $message = 'Erro ao registrar pedido.',
    ) {
        parent::__construct($message);
    }
}
