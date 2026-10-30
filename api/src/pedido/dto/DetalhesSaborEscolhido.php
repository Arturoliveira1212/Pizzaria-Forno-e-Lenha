<?php

class DetalhesSaborEscolhido {
    /**
     * @param string[] $ingredientesRemovidos
     * @param string[] $ingredientesSubstitutos
     * @param DetalhesAdicionalSabor[] $adicionais
     */
    public function __construct(
        public readonly string $nome,
        public readonly array $ingredientesRemovidos,
        public readonly array $ingredientesSubstitutos,
        public readonly array $adicionais,
    ) {}

    public static function criarComEntidade(SaborEscolhido $saborEscolhido): DetalhesSaborEscolhido {
        return new self(
            nome: $saborEscolhido->pizza->nome,
            ingredientesRemovidos: array_map(
                fn(Ingrediente $ingrediente): string => $ingrediente->nome,
                $saborEscolhido->obterIngredientesRemovidos(),
            ),
            ingredientesSubstitutos: array_map(
                fn(IngredienteEscolhido $ingredienteEscolhido): string => $ingredienteEscolhido->ingrediente->nome,
                $saborEscolhido->obterIngredientesEscolhidosPorPapel(PapelIngrediente::SUBSTITUTO),
            ),
            adicionais: DetalhesAdicionalSabor::criarComEntidades(
                $saborEscolhido->obterIngredientesEscolhidosPorPapel(PapelIngrediente::ADICIONAL),
            ),
        );
    }
}
