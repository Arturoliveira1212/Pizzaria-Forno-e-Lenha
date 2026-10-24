<?php

class UnidadeMedida {
    public function __construct(
        public readonly int $id,
        public readonly string $descricao,
        public readonly string $sigla,
    ) {}

    /**
     * @param array{idUnidadeMedida: int, descricao: string, sigla: string} $dados
     */
    public static function criar(array $dados): UnidadeMedida {
        return new UnidadeMedida(
            id: (int) $dados['idUnidadeMedida'],
            descricao: $dados['descricao'],
            sigla: $dados['sigla'],
        );
    }
}
