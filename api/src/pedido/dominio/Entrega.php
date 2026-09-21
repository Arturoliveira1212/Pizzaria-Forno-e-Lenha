<?php

class Entrega {
    public function __construct(
        public readonly int $id,
        public readonly Endereco $endereco,
        public readonly Entregador $entregador,
        public readonly DateTime $dataSaida,
        public readonly ?DateTime $dataChegada,
        public readonly Dinheiro $taxa,
    ) {}
}
