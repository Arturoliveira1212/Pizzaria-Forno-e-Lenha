<?php

abstract class DescontoPedido {
    public function __construct(
        public readonly int $id,
        public readonly Dinheiro $valorDesconto,
    ) {}

    abstract public function aplicar(Dinheiro $valorTotal): Dinheiro;
}
