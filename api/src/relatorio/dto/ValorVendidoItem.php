<?php

class ValorVendidoItem {
    public function __construct(
        public readonly string $nome,
        public readonly Dinheiro $valor,
    ) {}
}
