<?php

class ItemPizzaParaCadastro {
    /**
     * @param SaborEscolhidoParaCadastro[] $saboresEscolhidos
     */
    public function __construct(
        public readonly string $tamanho,
        public readonly int $quantidade,
        public readonly array $saboresEscolhidos,
    ) {}

    /**
     * @param array<string, mixed> $dados
     */
    public static function criarComArray(array $dados): ItemPizzaParaCadastro {
        $saboresEscolhidos = array_map(function (StdClass $saborEscolhido): SaborEscolhidoParaCadastro {
            return SaborEscolhidoParaCadastro::criarComArray((array) $saborEscolhido);
        }, $dados['saboresEscolhidos'] ?? []);

        return new self(
            tamanho: $dados['tamanhoPizza'] ?? '',
            quantidade: isset($dados['quantidade']) ? (int) $dados['quantidade'] : 0,
            saboresEscolhidos: $saboresEscolhidos,
        );
    }
}
