<?php

abstract class DescontoPedido {
    public const MENOR_ID_PERMITIDO = 0;

    public function __construct(
        public readonly int $id,
        public readonly Dinheiro $valorDesconto,
    ) {
        $this->validar();
    }

    protected function validar(): void {
        $erros = [];

        if ($this->id < self::MENOR_ID_PERMITIDO) {
            $erros[] = 'O identificador do ingrediente escolhido deve ser maior que ' . self::MENOR_ID_PERMITIDO . '.';
        }

        if ($this->valorDesconto->ehIgualA(Dinheiro::zero())) {
            $erros[] = 'O valor do desconto não pode ser zero.';
        }

        if (!empty($erros)) {
            throw DominioException::criarException(...$erros);
        }
    }
}
