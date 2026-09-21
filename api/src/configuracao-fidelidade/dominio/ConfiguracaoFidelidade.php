<?php

class ConfiguracaoFidelidade {
    public function __construct(
        public readonly int $id,
        public readonly Dinheiro $valorMinimo,
        public readonly Percentual $percentual,
        public readonly int $validadeEmDias,
    ) {}
}
