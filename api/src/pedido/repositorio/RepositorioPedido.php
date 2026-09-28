<?php

interface RepositorioPedido {
    public function registrarPedido(Pedido $pedido): void;
}
