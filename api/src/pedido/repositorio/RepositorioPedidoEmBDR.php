<?php

class RepositorioPedidoEmBDR implements RepositorioPedido {
    public function __construct(
        private readonly BancoDados $bancoDados,
    ) {}

    public function registrarPedido(Pedido $pedido): void {}
}
