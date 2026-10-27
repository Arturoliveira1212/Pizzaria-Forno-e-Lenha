<?php

class SaborEscolhido {
    public const MENOR_ID_PERMITIDO = 0;

    /**
     * @param IngredienteEscolhido[] $ingredientesEscolhidos
     */
    public function __construct(
        public readonly int $id,
        public readonly Pizza $pizza,
        public readonly array $ingredientesEscolhidos,
    ) {
        $this->validar();
    }

    protected function validar(): void {
        $erros = [];

        if ($this->id < self::MENOR_ID_PERMITIDO) {
            $erros[] = 'O identificador do ingrediente escolhido deve ser maior que ' . self::MENOR_ID_PERMITIDO . '.';
        }

        $idsIngredientesPadrao = [];
        $idsIngredientesSubstitutos = [];

        foreach ($this->ingredientesEscolhidos as $ingredienteEscolhido) {
            match ($ingredienteEscolhido->papel) {
                PapelIngrediente::PADRAO => $this->validarIngredientePadrao($ingredienteEscolhido, $idsIngredientesPadrao, $erros),
                PapelIngrediente::SUBSTITUTO => $this->validarIngredienteSubstituto($ingredienteEscolhido, $idsIngredientesSubstitutos, $erros),
                PapelIngrediente::ADICIONAL => $this->validarIngredienteAdicional($ingredienteEscolhido, $erros),
            };
        }

        $diferenca = array_diff($this->pizza->obterIdsIngredientesPadraoNaoPersonalizaveis(), $idsIngredientesPadrao);
        if (!empty($diferenca)) {
            $erros[] = 'É necessário escolher todos os ingredientes padrão da pizza.';
        }

        $quantidadeIngredientesSubstitutosEscolhidos = count($idsIngredientesSubstitutos);
        $quantidadeMaximaIngredientesPadrao = count($this->pizza->ingredientesPadrao);
        $quantidadeIngredientesPadraoEscolhidos = count($idsIngredientesPadrao);

        $totalEscolhido = $quantidadeIngredientesPadraoEscolhidos + $quantidadeIngredientesSubstitutosEscolhidos;
        if ($totalEscolhido > $quantidadeMaximaIngredientesPadrao) {
            $erros[] = 'A quantidade total de ingredientes padrão e substitutos excede o número de vagas da pizza.';
        }

        if (!empty($erros)) {
            throw DominioException::criarException(...$erros);
        }
    }

    /**
     * @param int[] $idsIngredientesPadrao
     * @param string[] $erros
     */
    private function validarIngredientePadrao(IngredienteEscolhido $ingredienteEscolhido, array &$idsIngredientesPadrao, array &$erros): void {
        $idIngrediente = $ingredienteEscolhido->ingrediente->id;
        $nomeIngrediente = $ingredienteEscolhido->ingrediente->nome;

        if (in_array($idIngrediente, $idsIngredientesPadrao)) {
            $erros[] = "O ingrediente {$nomeIngrediente}({$idIngrediente}) já foi selecionado como padrão.";

            return;
        }

        if (!$this->pizza->temComoIngredientePadrao($ingredienteEscolhido->ingrediente)) {
            $erros[] = "O ingrediente {$nomeIngrediente}({$idIngrediente}) não é permitido como padrão para a pizza.";

            return;
        }

        $idsIngredientesPadrao[] = $idIngrediente;
    }

    /**
     * @param int[] $idsIngredientesSubstitutos
     * @param string[] $erros
     */
    private function validarIngredienteSubstituto(IngredienteEscolhido $ingredienteEscolhido, array &$idsIngredientesSubstitutos, array &$erros): void {
        $idIngrediente = $ingredienteEscolhido->ingrediente->id;
        $nomeIngrediente = $ingredienteEscolhido->ingrediente->nome;

        if (!$this->pizza->temComoIngredienteSubstituto($ingredienteEscolhido->ingrediente)) {
            $erros[] = "O ingrediente {$nomeIngrediente}({$idIngrediente}) não é permitido como substituto para a pizza.";

            return;
        }

        $idsIngredientesSubstitutos[] = $idIngrediente;
    }

    /**
     * @param string[] $erros
     */
    private function validarIngredienteAdicional(IngredienteEscolhido $ingredienteEscolhido, array &$erros): void {
        $idIngrediente = $ingredienteEscolhido->ingrediente->id;
        $nomeIngrediente = $ingredienteEscolhido->ingrediente->nome;

        $adicional = $this->pizza->obterAdicional($ingredienteEscolhido->ingrediente);
        if (!$adicional instanceof IngredienteAdicional) {
            $erros[] = "O ingrediente {$nomeIngrediente}({$idIngrediente}) não é permitido como adicional para a pizza.";

            return;
        }

        $precoAdicional = $adicional->preco;
        $precoMetadeAdicional = $precoAdicional->dividir(2);
        if (!$ingredienteEscolhido->custoAdicional->ehIgualA($precoAdicional) && !$ingredienteEscolhido->custoAdicional->ehIgualA($precoMetadeAdicional)) {
            $erros[] = "O custo adicional do ingrediente {$nomeIngrediente}({$idIngrediente}) não corresponde ao preço definido para a pizza.";

            return;
        }
    }

    public function calcularPrecoDosAdicionais(): Dinheiro {
        $preco = Dinheiro::zero();

        foreach ($this->ingredientesEscolhidos as $ingredienteEscolhido) {
            if ($ingredienteEscolhido->papel === PapelIngrediente::ADICIONAL) {
                $preco = $preco->somar($ingredienteEscolhido->custoAdicional);
            }
        }

        return $preco;
    }

    public function obterConsumo(IngredienteEscolhido $ingredienteEscolhido, TamanhoPizza $tamanhoPizza, int $quantidadeSabores, int $quantidadeItem): QuantidadeMedida {
        $ingrediente = $ingredienteEscolhido->ingrediente;
        $pizza = $this->pizza;

        $quantidade = match ($ingredienteEscolhido->papel) {
            PapelIngrediente::PADRAO => $pizza->obterIngredientePadrao($ingrediente)->obterQuantidadePorTamanho($tamanhoPizza)->quantidadeMedida,
            PapelIngrediente::SUBSTITUTO => $pizza->obterIngredienteSubstituto($ingrediente)->obterQuantidadePorTamanho($tamanhoPizza)->quantidadeMedida,
            PapelIngrediente::ADICIONAL => $pizza->obterAdicional($ingrediente)->quantidadeMedida,
        };

        return $quantidade->dividir($quantidadeSabores)->multiplicar($quantidadeItem);
    }
}
