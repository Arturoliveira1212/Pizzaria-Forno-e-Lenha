<?php

class Promocao {
    public function __construct(
        public readonly int $id,
        public readonly string $descricao,
        public readonly int $quantidadeMinima,
        public readonly TamanhoPizza $tamanhoPizza,
        public readonly Percentual $percentualDesconto,
        public readonly ?DateTime $dataInicio,
        public readonly ?DateTime $dataFim,
    ) {}
}
