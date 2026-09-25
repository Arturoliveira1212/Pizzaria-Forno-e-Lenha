<?php

class RestricoesParaBuscaClientes {
    public function __construct(
        public readonly ?string $identificador,
        public readonly ?int $pagina,
        public readonly ?int $tamanhoPagina,
    ) {}

    /**
     * @param array<string, mixed> $dados
     */
    public static function criarComArray(array $dados): RestricoesParaBuscaClientes {
        return new self(
            identificador: (isset($dados['identificador']) ? trim((string) $dados['identificador']) : null),
            pagina: isset($dados['pagina']) ? (int) $dados['pagina'] : null,
            tamanhoPagina: isset($dados['tamanhoPagina']) ? (int) $dados['tamanhoPagina'] : null,
        );
    }
}
