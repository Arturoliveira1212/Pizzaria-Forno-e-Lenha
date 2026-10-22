<?php

abstract class MovimentacaoEstoque {
    public function __construct(
        public readonly int $id,
        public readonly DateTime $data,
        public readonly TipoMovimentacaoEstoque $tipoMovimentacaoEstoque,
        public readonly Quantidade $quantidade,
    ) {}
}
