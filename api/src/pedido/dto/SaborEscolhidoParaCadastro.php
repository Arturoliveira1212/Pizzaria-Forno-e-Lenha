<?php

class SaborEscolhidoParaCadastro {
    /**
     * @param IngredienteEscolhidoParaCadastro[] $ingredientesEscolhidos
     */
    public function __construct(
        public readonly int $idPizza,
        public readonly array $ingredientesEscolhidos,
    ) {}

    /**
     * @param array<string, mixed> $dados
     */
    public static function criarComArray(array $dados): SaborEscolhidoParaCadastro {
        $ingredientesEscolhidos = array_map(function (StdClass $ingredienteEscolhido): IngredienteEscolhidoParaCadastro {
            return IngredienteEscolhidoParaCadastro::criarComArray((array) $ingredienteEscolhido);
        }, $dados['ingredientesEscolhidos'] ?? []);

        return new self(
            idPizza: isset($dados['idPizza']) ? (int) $dados['idPizza'] : 0,
            ingredientesEscolhidos: $ingredientesEscolhidos,
        );
    }
}
