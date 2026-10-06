<?php

interface RepositorioPedido {
    /**
     * @return PedidoParaListagem[]
     */
    public function obterPedidos(?int $idPedido = null): array;

    public function alterarStatus(int $idPedido, StatusPedido $novoStatus): void;

    public function registrarPedido(Pedido $pedido): void;
}
