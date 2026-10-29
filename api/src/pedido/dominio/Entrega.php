<?php

class Entrega {
    public const TAXA_ENTREGA_FIXA_EM_CENTAVOS = 1000;

    public function __construct(
        public readonly int $id,
        public readonly Endereco $endereco,
        public readonly Entregador $entregador,
        public readonly ?DateTime $dataSaida,
        public readonly ?DateTime $dataChegada,
        public readonly Dinheiro $taxa,
    ) {}

    public function copiarComDataChegada(DateTime $dataChegada): Entrega {
        return new Entrega(
            id: $this->id,
            endereco: $this->endereco,
            entregador: $this->entregador,
            dataSaida: $this->dataSaida,
            dataChegada: $dataChegada,
            taxa: $this->taxa,
        );
    }
}
