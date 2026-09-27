<?php

class Pizza {
    /**
     * @param IngredientePadrao[] $ingredientesPadrao
     * @param Ingrediente[] $ingredientesSubstitutos
     * @param IngredienteAdicional[] $adicionais
     * @param PrecoPorTamanho[] $precosPorTamanho
     */
    public function __construct(
        public readonly int $id,
        public readonly string $nome,
        public readonly string $descricao,
        public readonly array $ingredientesPadrao,
        public readonly array $ingredientesSubstitutos,
        public readonly array $adicionais,
        public readonly array $precosPorTamanho,
    ) {}

    public function obterAdicional(Ingrediente $ingrediente): ?IngredienteAdicional {
        foreach ($this->adicionais as $adicional) {
            if ($adicional->ingrediente->id === $ingrediente->id) {
                return $adicional;
            }
        }

        return null;
    }

    public function temComoIngredientePadrao(Ingrediente $ingrediente): bool {
        foreach ($this->ingredientesPadrao as $ingredientePadrao) {
            if ($ingredientePadrao->ingrediente->id === $ingrediente->id) {
                return true;
            }
        }

        return false;
    }

    public function temComoIngredienteSubstituto(Ingrediente $ingrediente): bool {
        foreach ($this->ingredientesSubstitutos as $ingredienteSubstituto) {
            if ($ingredienteSubstituto->id === $ingrediente->id) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return int[]
     */
    public function obterIdsIngredientesPadraoNaoPersonalizaveis(): array {
        $idsIngredientesNaoPersonalizaveis = [];

        foreach ($this->ingredientesPadrao as $ingredientePadrao) {
            if (!$ingredientePadrao->substituivel && !$ingredientePadrao->removivel) {
                $idsIngredientesNaoPersonalizaveis[] = $ingredientePadrao->ingrediente->id;
            }
        }

        return $idsIngredientesNaoPersonalizaveis;
    }

    public function obterPrecoPorTamanho(TamanhoPizza $tamanho): ?Dinheiro {
        foreach ($this->precosPorTamanho as $precoPorTamanho) {
            if ($precoPorTamanho->tamanhoPizza === $tamanho) {
                return $precoPorTamanho->preco;
            }
        }

        return null;
    }
}
