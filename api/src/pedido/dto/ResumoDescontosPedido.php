<?php

class ResumoDescontosPedido {
    /**
     * @param ResumoDescontoPromocao[] $promocoes
     */
    public function __construct(
        public readonly float $valorTotal,
        public readonly array $promocoes = [],
        public readonly ?ResumoDescontoFidelidade $fidelidade = null,
    ) {}

    public static function criarComEntidade(Pedido $pedido): ResumoDescontosPedido {
        $resumoDescontosPromocao = array_map(function (DescontoPromocao $desconto): ResumoDescontoPromocao {
            return ResumoDescontoPromocao::criarComEntidade($desconto);
        }, $pedido->descontosPromocao);

        $resumoFidelidade = $pedido->descontoFidelidade
            ? ResumoDescontoFidelidade::criarComEntidade($pedido->descontoFidelidade)
            : null;

        return new self(
            valorTotal: $pedido->calcularValorTotal()->converterParaReais(),
            promocoes: $resumoDescontosPromocao,
            fidelidade: $resumoFidelidade,
        );
    }
}
