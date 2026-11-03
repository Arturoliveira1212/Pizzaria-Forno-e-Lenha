<?php

class PeriodoParaRelatorio {
    public function __construct(
        public readonly string $dataInicio,
        public readonly string $dataFim,
    ) {}

    /**
     * @param array<string, mixed> $dados
     */
    public static function criarComArray(array $dados): PeriodoParaRelatorio {
        return new self(
            dataInicio: !empty($dados['dataInicio']) ? trim((string) $dados['dataInicio']) : date('Y-m-01'),
            dataFim: !empty($dados['dataFim']) ? trim((string) $dados['dataFim']) : date('Y-m-t'),
        );
    }
}
