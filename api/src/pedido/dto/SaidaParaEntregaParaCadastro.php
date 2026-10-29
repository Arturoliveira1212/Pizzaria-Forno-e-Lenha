<?php

class SaidaParaEntregaParaCadastro {
    public function __construct(
        public readonly int $idPedido,
        public readonly int $idEntregador,
    ) {}

    /**
     * @param array<string, mixed> $dados
     */
    public static function criarComArray(array $dados): SaidaParaEntregaParaCadastro {
        return new self(
            idPedido: isset($dados['idPedido']) ? (int) $dados['idPedido'] : 0,
            idEntregador: isset($dados['idEntregador']) ? (int) $dados['idEntregador'] : 0,
        );
    }
}
