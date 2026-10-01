<?php

class CalculadoraDescontoFidelidade {
    /**
     * @param ItemPedido[] $itensPedido
     */
    public function calcularDesconto(array $itensPedido, BeneficioFidelidade $beneficioFidelidade, Dinheiro $valorTotalDescontoPromocao): ?DescontoFidelidade {
        $valorTotal = Dinheiro::zero();

        foreach ($itensPedido as $itemPedido) {
            $valorTotal = $valorTotal->somar($itemPedido->calcularPrecoTotal());
        }

        $valorTotal = $valorTotal->subtrair($valorTotalDescontoPromocao);
        $valorDesconto = $beneficioFidelidade->percentualDesconto->calcularSobre($valorTotal);

        return new DescontoFidelidade(
            id: 0,
            valorDesconto: $valorDesconto,
            beneficioFidelidade: $beneficioFidelidade,
        );
    }
}
