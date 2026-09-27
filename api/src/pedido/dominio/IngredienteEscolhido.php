<?php

class IngredienteEscolhido {
    public const MENOR_ID_PERMITIDO = 0;

    public function __construct(
        public readonly int $id,
        public readonly Ingrediente $ingrediente,
        public readonly PapelIngrediente $papel,
        public readonly Dinheiro $custoAdicional,
    ) {
        $this->validar();
    }

    protected function validar(): void {
        $erros = [];

        if ($this->id < self::MENOR_ID_PERMITIDO) {
            $erros[] = 'O identificador do ingrediente escolhido deve ser maior que ' . self::MENOR_ID_PERMITIDO . '.';
        }

        if (in_array($this->papel, [PapelIngrediente::PADRAO, PapelIngrediente::SUBSTITUTO]) && $this->custoAdicional->ehMaiorQue(Dinheiro::zero())) {
            $erros[] = 'O custo do ingrediente escolhido não pode ser maior que zero.';
        }

        if ($this->papel === PapelIngrediente::ADICIONAL && $this->custoAdicional->ehIgualA(Dinheiro::zero())) {
            $erros[] = 'O custo do ingrediente adicional não pode ser igual a zero.';
        }

        if (!empty($erros)) {
            throw DominioException::criarException(...$erros);
        }
    }
}
