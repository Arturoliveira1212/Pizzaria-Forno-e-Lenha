<?php

class Percentual {
    public const MENOR_VALOR_PERMITIDO = 1;
    public const MAIOR_VALOR_PERMITIDO = 100;

    public function __construct(
        public readonly int $valor,
    ) {
        $this->validar();
    }

    protected function validar(): void {
        $erros = [];

        if ($this->valor < self::MENOR_VALOR_PERMITIDO || $this->valor > self::MAIOR_VALOR_PERMITIDO) {
            $erros[] = 'O valor do percentual deve estar entre ' . self::MENOR_VALOR_PERMITIDO . ' e ' . self::MAIOR_VALOR_PERMITIDO . '.';
        }

        if (!empty($erros)) {
            throw DominioException::criarException(...$erros);
        }
    }

    public function paraFracao(): float {
        return $this->valor / 100;
    }

    public function calcularSobre(Dinheiro $valor): Dinheiro {
        return $valor->multiplicar($this->paraFracao());
    }

    public function somarSobre(Dinheiro $valor): Dinheiro {
        return $valor->somar($this->calcularSobre($valor));
    }

    public function subtrairDe(Dinheiro $valor): Dinheiro {
        return $valor->subtrair($this->calcularSobre($valor));
    }
}
