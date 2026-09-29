<?php

class Pedido {
    public const MENOR_ID_PERMITIDO = 0;

    /**
     * @param ItemPedido[] $itensPedido
     * @param DescontoPromocao[] $descontosPromocao
     */
    public function __construct(
        public readonly int $id,
        public readonly DateTime $data,
        public readonly TipoAtendimento $tipoAtendimento,
        public readonly ?Cliente $cliente,
        public readonly array $itensPedido,
        public readonly StatusPedido $status,
        public readonly array $descontosPromocao = [],
        public readonly ?DescontoFidelidade $descontoFidelidade = null,
        public readonly ?Entrega $entrega = null,
        public readonly ?Cancelamento $cancelamento = null,
    ) {
        $this->validar();
    }

    protected function validar(): void {
        $erros = [];

        if ($this->id < self::MENOR_ID_PERMITIDO) {
            $erros[] = 'O identificador do ingrediente escolhido deve ser maior que ' . self::MENOR_ID_PERMITIDO . '.';
        }

        if ($this->data > new DateTime()) {
            $erros[] = 'A data do pedido não pode ser futura.';
        }

        if ($this->tipoAtendimento->exigeCliente() && !$this->cliente instanceof Cliente) {
            $erros[] = 'O pedido exige um cliente.';
        }

        if (empty($this->itensPedido)) {
            $erros[] = 'O pedido deve conter pelo menos um item.';
        }

        if ($this->descontoFidelidade instanceof DescontoFidelidade && !$this->cliente instanceof Cliente) {
            $erros[] = 'O desconto de fidelidade exige um cliente.';
        }

        if ($this->entrega instanceof Entrega && !$this->cliente instanceof Cliente) {
            $erros[] = 'A entrega exige um cliente.';
        }

        if ($this->cancelamento instanceof Cancelamento && $this->status !== StatusPedido::CANCELADO) {
            $erros[] = 'O cancelamento só pode ser registrado se o status do pedido for CANCELADO.';
        }

        if (!empty($erros)) {
            throw DominioException::criarException(...$erros);
        }
    }

    public function temCliente(): bool {
        return $this->cliente instanceof Cliente;
    }

    public function temDescontoFidelidade(): bool {
        return $this->descontoFidelidade instanceof DescontoFidelidade;
    }

    public function calcularValorTotal(): Dinheiro {
        $valorTotal = Dinheiro::zero();

        foreach ($this->itensPedido as $itemPedido) {
            $valorTotal = $valorTotal->somar($itemPedido->calcularPrecoTotal());
        }

        foreach ($this->descontosPromocao as $descontoPromocao) {
            $valorTotal = $valorTotal->subtrair($descontoPromocao->valorDesconto);
        }

        if ($this->descontoFidelidade instanceof DescontoFidelidade) {
            $valorTotal = $valorTotal->subtrair($this->descontoFidelidade->valorDesconto);
        }

        return $valorTotal;
    }
}
