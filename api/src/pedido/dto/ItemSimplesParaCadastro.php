<?php

class ItemSimplesParaCadastro {
    public function __construct(
        public readonly int $idProduto,
        public readonly int $quantidade,
    ) {}

    /**
     * @param array<string, mixed> $dados
     */
    public static function criarComArray(array $dados): ItemSimplesParaCadastro {
        return new self(
            idProduto: isset($dados['idProduto']) ? (int) $dados['idProduto'] : 0,
            quantidade: isset($dados['quantidade']) ? (int) $dados['quantidade'] : 0,
        );
    }
}
