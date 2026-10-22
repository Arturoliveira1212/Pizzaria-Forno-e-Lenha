<?php

class UnidadeMedida {
    public function __construct(
        public readonly int $id,
        public readonly string $descricao,
        public readonly string $sigla,
    ) {}
}
