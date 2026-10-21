<?php

class ItemPizza extends ItemPedido {
    public const MINIMO_SABORES_PERMITIDOS = 1;
    public const MAXIMO_SABORES_PERMITIDOS = 2;

    /**
     * @param SaborEscolhido[] $saboresEscolhidos
     */
    public function __construct(
        int $id,
        int $quantidade,
        public readonly array $saboresEscolhidos,
        public readonly TamanhoPizza $tamanhoPizza,
        ?Dinheiro $precoPraticado = null,
    ) {
        parent::__construct($id, $quantidade, $precoPraticado);
    }

    protected function validarCamposEspecificos(array &$erros): void {
        $quantidadeSabores = count($this->saboresEscolhidos);
        if ($quantidadeSabores < self::MINIMO_SABORES_PERMITIDOS || $quantidadeSabores > self::MAXIMO_SABORES_PERMITIDOS) {
            $erros[] = 'A quantidade de sabores deve ser entre ' . self::MINIMO_SABORES_PERMITIDOS . ' e ' . self::MAXIMO_SABORES_PERMITIDOS . '.';
        }

        $idsSaboresEscolhidos = [];

        foreach ($this->saboresEscolhidos as $saborEscolhido) {
            $nome = $saborEscolhido->pizza->nome;

            if (in_array($saborEscolhido->pizza->id, $idsSaboresEscolhidos, true)) {
                $erros[] = "O sabor {$nome} foi escolhido mais de uma vez.";
                continue;
            }

            if (!$saborEscolhido->pizza->temTamanho($this->tamanhoPizza)) {
                $erros[] = "O sabor {$nome} não está disponível para o tamanho selecionado.";
                continue;
            }

            $idsSaboresEscolhidos[] = $saborEscolhido->pizza->id;
        }
    }

    protected function calcularPrecoUnitario(): Dinheiro {
        $preco = Dinheiro::zero();

        $quantidadeSabores = count($this->saboresEscolhidos);

        foreach ($this->saboresEscolhidos as $saborEscolhido) {
            $precoPizza = $saborEscolhido->pizza->obterPrecoPorTamanho($this->tamanhoPizza)->dividir($quantidadeSabores);
            $precoAdicionais = $saborEscolhido->calcularPrecoDosAdicionais();

            $preco = $preco->somar($precoPizza->somar($precoAdicionais));
        }

        return $preco;
    }
}
