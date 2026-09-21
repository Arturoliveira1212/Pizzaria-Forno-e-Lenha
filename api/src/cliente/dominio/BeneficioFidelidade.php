<?php

class BeneficioFidelidade {
    public function __construct(
        public readonly int $id,
        public readonly Percentual $percentualDesconto,
        public readonly DateTime $dataConcessao,
        public readonly DateTime $validade,
        public readonly bool $utilizado,
    ) {}
}
