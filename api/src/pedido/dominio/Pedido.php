<?php

class Pedido {
    /**
     * @param ItemPedido[] $itensPedidos
     * @param DescontoPromocao[] $descontosPromocao
     */
    public function __construct(
        public readonly string $id,
        public readonly DateTime $data,
        public readonly TipoAtendimento $tipoAtendimento,
        public readonly Cliente $cliente,
        public readonly array $itensPedidos,
        public readonly StatusPedido $status,
        public readonly array $descontosPromocao,
        public readonly ?Entrega $entrega,
        public readonly ?Cancelamento $cancelamento,
    ) {}
}
