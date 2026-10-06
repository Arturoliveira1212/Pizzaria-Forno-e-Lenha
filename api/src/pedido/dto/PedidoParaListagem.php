<?php

class PedidoParaListagem {
    public readonly float $valorTotal;

    /**
     * @param string[] $proximosStatus
    */
    public function __construct(
        public readonly int $id,
        public readonly string $data,
        public readonly ?string $nomeCliente,
        public readonly string $tipoAtendimento,
        public readonly string $status,
        public readonly array $proximosStatus,
        int $valorTotalCentavos,
    ) {
        $this->valorTotal = $valorTotalCentavos / 100;
    }
}
