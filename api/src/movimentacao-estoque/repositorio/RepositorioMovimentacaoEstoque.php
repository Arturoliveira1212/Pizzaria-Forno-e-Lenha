<?php

interface RepositorioMovimentacaoEstoque {
    public function registrarMovimentacaoEstoque(Pedido $pedido, MovimentacaoEstoque $movimentacao): void;
}
