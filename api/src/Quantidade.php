<?php

class Quantidade {
    public function __construct(
        public readonly int $valor,
        public readonly UnidadeMedida $unidadeMedida,
    ) {}

    /**
     * @param array{idUnidadeMedida: int, descricao: string, sigla: string, valor: int} $dados
     */
    public static function criar(array $dados): Quantidade {
        return new Quantidade(
            valor: (int) $dados['valor'],
            unidadeMedida: UnidadeMedida::criar($dados),
        );
    }
}
