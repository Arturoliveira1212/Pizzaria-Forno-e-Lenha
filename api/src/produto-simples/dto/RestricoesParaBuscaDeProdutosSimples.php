<?php

class RestricoesParaBuscaDeProdutosSimples {
    public function __construct(
        public readonly ?string $tipo,
        public readonly ?string $identificador,
        public readonly ?int $pagina,
        public readonly ?int $limite,
    ) {}

    /**
     * @param array<string, mixed> $dados
     */
    public static function criarComArray(array $dados): RestricoesParaBuscaDeProdutosSimples {
        return new self(
            tipo: isset($dados['tipo']) ? strtoupper(trim((string) $dados['tipo'])) : null,
            identificador: (isset($dados['identificador']) ? trim((string) $dados['identificador']) : null),
            pagina: isset($dados['pagina']) ? (int) $dados['pagina'] : null,
            limite: isset($dados['limite']) ? (int) $dados['limite'] : null,
        );
    }
}
