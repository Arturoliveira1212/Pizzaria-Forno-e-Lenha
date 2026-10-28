<?php

class CancelamentoParaCadastro {
    public function __construct(
        public readonly int $idPedido,
        public readonly string $motivo,
    ) {}

    /**
     * @param array<string, mixed> $dados
     */
    public static function criarComArray(array $dados): CancelamentoParaCadastro {
        return new self(
            idPedido: isset($dados['idPedido']) ? (int) $dados['idPedido'] : 0,
            motivo: isset($dados['motivo']) ? trim((string) $dados['motivo']) : '',
        );
    }
}
