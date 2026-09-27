<?php

abstract class ItemPedido {
    public const MENOR_ID_PERMITIDO = 0;
    public const QUANTIDADE_MINIMA_PERMITIDA = 1;

    public readonly ?Dinheiro $precoPraticado;

    public function __construct(
        public readonly int $id,
        public readonly int $quantidade,
        ?Dinheiro $precoPraticado = null,
    ) {
        $this->validar();

        $this->precoPraticado = $precoPraticado instanceof Dinheiro
            ? $precoPraticado
            : $this->calcularPrecoUnitario();

        $this->validarPrecoPraticado();
    }

    protected function validar(): void {
        $erros = [];

        $this->validarCamposComuns($erros);
        $this->validarCamposEspecificos($erros);

        if (!empty($erros)) {
            throw DominioException::criarException(...$erros);
        }
    }

    protected function validarPrecoPraticado(): void {
        if ($this->precoPraticado->ehIgualA(Dinheiro::zero())) {
            throw new DominioException('O preço praticado não pode ser zero.');
        }
    }

    /**
     * @param string[] $erros
     */
    protected function validarCamposComuns(array &$erros): void {
        if ($this->id < self::MENOR_ID_PERMITIDO) {
            $erros[] = 'O identificador do ingrediente escolhido deve ser maior que ' . self::MENOR_ID_PERMITIDO . '.';
        }

        if ($this->quantidade < self::QUANTIDADE_MINIMA_PERMITIDA) {
            $erros[] = 'A quantidade deve ser maior ou igual a ' . self::QUANTIDADE_MINIMA_PERMITIDA . '.';
        }
    }

    /**
     * @param string[] $erros
     */
    abstract protected function validarCamposEspecificos(array &$erros): void;

    abstract protected function calcularPrecoUnitario(): Dinheiro;

    public function calcularPrecoTotal(): Dinheiro {
        return $this->precoPraticado->multiplicar($this->quantidade);
    }
}
