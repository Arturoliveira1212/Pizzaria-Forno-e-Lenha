<?php

class GeradorBeneficioFidelidade {
    public function gerarBeneficioFidelidade(Pedido $pedido, ConfiguracaoFidelidade $configuracaoFidelidade): ?BeneficioFidelidade {
        if ($pedido->calcularValorTotal()->ehMenorQue($configuracaoFidelidade->valorMinimo)) {
            return null;
        }

        return new BeneficioFidelidade(
            id: 0,
            percentualDesconto: $configuracaoFidelidade->percentual,
            dataConcessao: new DateTime(),
            validade: new DateTime()->modify('+' . $configuracaoFidelidade->validadeEmDias . ' days'),
            utilizado: false,
        );
    }
}
