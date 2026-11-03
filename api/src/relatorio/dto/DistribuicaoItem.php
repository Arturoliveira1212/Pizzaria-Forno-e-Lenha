<?php

class DistribuicaoItem {
    public function __construct(
        public readonly string $nome,
        public readonly Dinheiro $valor,
        public readonly float $percentual,
    ) {}
}
