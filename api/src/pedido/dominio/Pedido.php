<?php

class Pedido {
    public const MENOR_ID_PERMITIDO = 0;

    /**
     * @param ItemPedido[] $itensPedido
     * @param DescontoPromocao[] $descontosPromocao
     * @param MovimentacaoEstoque[] $movimentacoesEstoque
     */
    public function __construct(
        public readonly int $id,
        public readonly Usuario $usuario,
        public readonly DateTime $data,
        public readonly TipoAtendimento $tipoAtendimento,
        public readonly ?Cliente $cliente,
        public readonly array $itensPedido,
        public readonly StatusPedido $status,
        public readonly array $descontosPromocao = [],
        public readonly ?DescontoFidelidade $descontoFidelidade = null,
        public readonly ?Entrega $entrega = null,
        public readonly ?Cancelamento $cancelamento = null,
        public readonly ?Dinheiro $taxaEntrega = null,
        public readonly array $movimentacoesEstoque = [],
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

    public function calcularValorDescontos(): Dinheiro {
        $valorDescontos = Dinheiro::zero();

        foreach ($this->descontosPromocao as $descontoPromocao) {
            $valorDescontos = $valorDescontos->somar($descontoPromocao->valorDesconto);
        }

        if ($this->descontoFidelidade instanceof DescontoFidelidade) {
            $valorDescontos = $valorDescontos->somar($this->descontoFidelidade->valorDesconto);
        }

        return $valorDescontos;
    }

    public function calcularValorEntrega(): Dinheiro {
        return $this->tipoAtendimento === TipoAtendimento::ENTREGA
            ? new Dinheiro(Entrega::TAXA_ENTREGA_FIXA_EM_CENTAVOS)
            : Dinheiro::zero();
    }

    public function calcularValorTotal(): Dinheiro {
        $valorTotal = Dinheiro::zero();

        foreach ($this->itensPedido as $itemPedido) {
            $valorTotal = $valorTotal->somar($itemPedido->calcularPrecoTotal());
        }

        $valorTotal = $valorTotal->subtrair($this->calcularValorDescontos());
        $valorTotal = $valorTotal->somar($this->calcularValorEntrega());

        return $valorTotal;
    }

    public function copiarComId(int $novoId): Pedido {
        return new Pedido(
            id: $novoId,
            usuario: $this->usuario,
            data: $this->data,
            tipoAtendimento: $this->tipoAtendimento,
            cliente: $this->cliente,
            itensPedido: $this->itensPedido,
            status: $this->status,
            descontosPromocao: $this->descontosPromocao,
            descontoFidelidade: $this->descontoFidelidade,
            entrega: $this->entrega,
            cancelamento: $this->cancelamento,
            taxaEntrega: $this->taxaEntrega,
            movimentacoesEstoque: $this->movimentacoesEstoque,
        );
    }

    public function obterMovimentacoesEstoque(?TipoOperacaoEstoque $tipoOperacaoEstoque): array {
        if ($tipoOperacaoEstoque === null) {
            return $this->movimentacoesEstoque;
        }

        return array_values(array_filter($this->movimentacoesEstoque, function (MovimentacaoEstoque $movimentacao) use ($tipoOperacaoEstoque) {
            return $movimentacao->tipoOperacaoEstoque === $tipoOperacaoEstoque;
        }));
    }
}
