<?php

class Quantidade {
    public function __construct(
        public readonly int $valor,
        public readonly UnidadeMedida $unidadeMedida,
    ) {}
}
