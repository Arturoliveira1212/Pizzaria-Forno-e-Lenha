<?php

abstract class MovimentacaoEstoque {
    public function __construct(
        public readonly int $id,
        public readonly DateTime $data,
        public readonly TipoOperacaoEstoque $tipoOperacaoEstoque,
        public readonly QuantidadeMedida $quantidadeMedida,
    ) {}
}
