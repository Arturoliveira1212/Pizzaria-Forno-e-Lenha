<?php

class ResumoDescontoFidelidade {
    public function __construct(
        public readonly Dinheiro $valorDesconto,
        public readonly Percentual $percentualDesconto,
    ) {}

    public static function criarComEntidade(DescontoFidelidade $descontoFidelidade): ResumoDescontoFidelidade {
        return new self(
            valorDesconto: $descontoFidelidade->valorDesconto,
            percentualDesconto: $descontoFidelidade->beneficioFidelidade->percentualDesconto,
        );
    }
}
