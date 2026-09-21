<?php

class DescontoPromocao extends DescontoPedido {
    public function __construct(
        int $id,
        Dinheiro $valorDesconto,
        public readonly Promocao $promocao,
    ) {
        parent::__construct($id, $valorDesconto);
    }

    public function aplicar(Dinheiro $valorTotal): Dinheiro {
        return new Dinheiro(0);
    }
}
