<?php

class DescontoFidelidade extends DescontoPedido {
    public function __construct(
        int $id,
        Dinheiro $valorDesconto,
        public readonly BeneficioFidelidade $beneficioFidelidade,
    ) {
        parent::__construct($id, $valorDesconto);
    }

    public function aplicar(Dinheiro $valorTotal): Dinheiro {
        return new Dinheiro(0);
    }
}
