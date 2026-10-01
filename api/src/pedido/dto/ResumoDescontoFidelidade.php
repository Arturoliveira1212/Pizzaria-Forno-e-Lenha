<?php

class ResumoDescontoFidelidade {
    public function __construct(
        public readonly float $valorDesconto,
        public readonly int $percentualDesconto,
    ) {}

    public static function criarComEntidade(DescontoFidelidade $descontoFidelidade): ResumoDescontoFidelidade {
        return new self(
            valorDesconto: $descontoFidelidade->valorDesconto->converterParaReais(),
            percentualDesconto: $descontoFidelidade->beneficioFidelidade->percentualDesconto->valor,
        );
    }
}
