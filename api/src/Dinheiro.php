<?php

class Dinheiro {
    public const MENOR_VALOR_PERMITIDO = 0;

    public function __construct(
        public readonly int $centavos,
    ) {
        $this->validar();
    }

    protected function validar(): void {
        $erros = [];

        if ($this->centavos < self::MENOR_VALOR_PERMITIDO) {
            $erros[] = 'O valor em centavos não pode ser negativo.';
        }

        if (!empty($erros)) {
            throw DominioException::criarException(...$erros);
        }
    }

    public static function zero(): Dinheiro {
        return new Dinheiro(0);
    }

    public static function emReais(float $reais): Dinheiro {
        return new Dinheiro((int) round($reais * 100));
    }

    public function converterParaReais(): float {
        return $this->centavos / 100;
    }

    public function somar(Dinheiro $outro): Dinheiro {
        return new Dinheiro($this->centavos + $outro->centavos);
    }

    public function subtrair(Dinheiro $outro): Dinheiro {
        $resultado = max(0, $this->centavos - $outro->centavos);

        return new Dinheiro($resultado);
    }

    public function multiplicar(float $fator): Dinheiro {
        if ($fator <= 0) {
            throw new DominioException('Não é possível multiplicar por número não positivo.');
        }

        return new Dinheiro((int) round($this->centavos * $fator));
    }

    public function dividir(float $fator): Dinheiro {
        if ($fator <= 0) {
            throw new DominioException('Não é possível dividir por número não positivo.');
        }

        return new Dinheiro((int) round($this->centavos / $fator));
    }

    public function ehIgualA(Dinheiro $outro): bool {
        return $this->centavos === $outro->centavos;
    }

    public function ehMenorQue(Dinheiro $outro): bool {
        return $this->centavos < $outro->centavos;
    }

    public function ehMenorOuIgualA(Dinheiro $outro): bool {
        return $this->centavos <= $outro->centavos;
    }

    public function ehMaiorQue(Dinheiro $outro): bool {
        return $this->centavos > $outro->centavos;
    }

    public function ehMaiorOuIgualA(Dinheiro $outro): bool {
        return $this->centavos >= $outro->centavos;
    }

    public function formatar(): string {
        return 'R$ ' . number_format($this->centavos / 100, 2, ',', '.');
    }
}
