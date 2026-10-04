<?php

class ResumoValoresPedido {
    /**
     * @param ResumoDescontoPromocao[] $promocoes
     */
    public function __construct(
        public readonly Dinheiro $valorTotalDescontos,
        public readonly Dinheiro $taxaEntrega,
        public readonly Dinheiro $valorTotal,
        public readonly array $promocoes = [],
        public readonly ?ResumoDescontoFidelidade $fidelidade = null,
    ) {}

    public static function criarComEntidade(Pedido $pedido): ResumoValoresPedido {
        $resumoDescontosPromocao = array_map(function (DescontoPromocao $desconto): ResumoDescontoPromocao {
            return ResumoDescontoPromocao::criarComEntidade($desconto);
        }, $pedido->descontosPromocao);

        $resumoFidelidade = $pedido->descontoFidelidade
            ? ResumoDescontoFidelidade::criarComEntidade($pedido->descontoFidelidade)
            : null;

        return new self(
            promocoes: $resumoDescontosPromocao,
            fidelidade: $resumoFidelidade,
            valorTotalDescontos: $pedido->calcularValorDescontos(),
            taxaEntrega: $pedido->calcularValorEntrega(),
            valorTotal: $pedido->calcularValorTotal(),
        );
    }
}
