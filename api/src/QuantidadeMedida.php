<?php

class QuantidadeMedida {
    public const MENOR_VALOR_PERMITIDO = 0;

    public function __construct(
        public readonly int $valor,
        public readonly UnidadeMedida $unidadeMedida,
    ) {
        $this->validar();
    }

    protected function validar(): void {
        $erros = [];

        if ($this->valor < self::MENOR_VALOR_PERMITIDO) {
            $erros[] = 'O valor não pode ser negativo.';
        }

        if (!empty($erros)) {
            throw DominioException::criarException(...$erros);
        }
    }

    private function validarOperacao(QuantidadeMedida $outra): void {
        if ($this->unidadeMedida->id !== $outra->unidadeMedida->id) {
            throw new DominioException('As unidades de medida devem ser iguais para comparar quantidades.');
        }
    }

    public function ehMenorOuIgualA(QuantidadeMedida $outra): bool {
        $this->validarOperacao($outra);

        return $this->valor <= $outra->valor;
    }

    public function ehMaiorQueZero(): bool {
        return $this->valor > 0;
    }
}
