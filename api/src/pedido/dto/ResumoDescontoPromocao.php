<?php

class ResumoDescontoPromocao {
    public function __construct(
        public readonly string $descricao,
        public readonly Dinheiro $valorDesconto,
        public readonly int $percentualDesconto,
    ) {}

    public static function criarComEntidade(DescontoPromocao $descontoPromocao): ResumoDescontoPromocao {
        return new self(
            descricao: $descontoPromocao->promocao->descricao,
            valorDesconto: $descontoPromocao->valorDesconto,
            percentualDesconto: $descontoPromocao->promocao->percentualDesconto->valor,
        );
    }
}
