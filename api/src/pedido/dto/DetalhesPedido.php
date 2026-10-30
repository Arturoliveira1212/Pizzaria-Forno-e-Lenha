<?php

class DetalhesPedido {
    /**
     * @param DetalhesItemPedido[] $itens
     * @param ResumoDescontoPromocao[] $promocoes
     */
    public function __construct(
        public readonly int $id,
        public readonly string $data,
        public readonly string $tipoAtendimento,
        public readonly string $status,
        public readonly string $nomeAtendente,
        public readonly ?string $nomeCliente,
        public readonly ?Telefone $telefoneCliente,
        public readonly ?Endereco $endereco,
        public readonly array $itens,
        public readonly Dinheiro $subtotal,
        public readonly array $promocoes,
        public readonly ?ResumoDescontoFidelidade $fidelidade,
        public readonly Dinheiro $taxaEntrega,
        public readonly Dinheiro $valorTotal,
        public readonly ?string $nomeEntregador,
        public readonly ?string $dataSaida,
        public readonly ?string $dataChegada,
        public readonly ?string $motivoCancelamento,
        public readonly ?string $dataCancelamento,
    ) {}

    public static function criarComEntidade(Pedido $pedido): DetalhesPedido {
        $subtotal = Dinheiro::zero();
        foreach ($pedido->itensPedido as $itemPedido) {
            $subtotal = $subtotal->somar($itemPedido->calcularPrecoTotal());
        }

        return new self(
            id: $pedido->id,
            data: $pedido->data->format('Y-m-d H:i:s'),
            tipoAtendimento: $pedido->tipoAtendimento->value,
            status: $pedido->status->value,
            nomeAtendente: $pedido->usuario->nome,
            nomeCliente: $pedido->cliente?->nome,
            telefoneCliente: $pedido->cliente?->telefone,
            endereco: $pedido->entrega->endereco ?? $pedido->cliente?->endereco,
            itens: array_map(
                fn(ItemPedido $itemPedido): DetalhesItemPedido => DetalhesItemPedido::criarComEntidade($itemPedido),
                $pedido->itensPedido,
            ),
            subtotal: $subtotal,
            promocoes: array_map(
                fn(DescontoPromocao $descontoPromocao): ResumoDescontoPromocao => ResumoDescontoPromocao::criarComEntidade($descontoPromocao),
                $pedido->descontosPromocao,
            ),
            fidelidade: $pedido->descontoFidelidade instanceof DescontoFidelidade
                ? ResumoDescontoFidelidade::criarComEntidade($pedido->descontoFidelidade)
                : null,
            taxaEntrega: $pedido->calcularValorEntrega(),
            valorTotal: $pedido->calcularValorTotal(),
            nomeEntregador: $pedido->entrega?->entregador->nome,
            dataSaida: $pedido->entrega?->dataSaida?->format('Y-m-d H:i:s'),
            dataChegada: $pedido->entrega?->dataChegada?->format('Y-m-d H:i:s'),
            motivoCancelamento: $pedido->cancelamento?->motivo,
            dataCancelamento: $pedido->cancelamento?->data->format('Y-m-d H:i:s'),
        );
    }
}
