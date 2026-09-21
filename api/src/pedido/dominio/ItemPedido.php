<?php

abstract class ItemPedido {
    public function __construct(
        public readonly int $id,
        public readonly int $quantidade,
    ) {}

    abstract public function calcularTotal(): Dinheiro;
}
