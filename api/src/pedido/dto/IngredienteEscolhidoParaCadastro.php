<?php

class IngredienteEscolhidoParaCadastro {
    public function __construct(
        public readonly int $idIngrediente,
        public readonly string $papel,
    ) {}

    /**
     * @param array<string, mixed> $dados
     */
    public static function criarComArray(array $dados): IngredienteEscolhidoParaCadastro {
        return new self(
            idIngrediente: isset($dados['idIngrediente']) ? (int) $dados['idIngrediente'] : 0,
            papel: $dados['papel'] ?? '',
        );
    }
}
