<?php

class TotalPedidosDia {
    public function __construct(
        public readonly string $data,
        public readonly Dinheiro $valorTotal,
    ) {}
}
