<?php

class RestricoesParaBuscaDePromocoes {
    public function __construct(
        public readonly ?bool $apenasVigentes,
        public readonly ?int $pagina,
        public readonly ?int $limite,
    ) {}

    /**
     * @param array<string, mixed> $dados
     */
    public static function criarComArray(array $dados): RestricoesParaBuscaDePromocoes {
        return new self(
            apenasVigentes: (isset($dados['apenasVigentes']) ? filter_var($dados['apenasVigentes'], FILTER_VALIDATE_BOOLEAN) : null),
            pagina: isset($dados['pagina']) ? (int) $dados['pagina'] : null,
            limite: isset($dados['limite']) ? (int) $dados['limite'] : null,
        );
    }
}
