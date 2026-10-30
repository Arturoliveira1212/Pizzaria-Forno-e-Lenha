<?php

class DetalhesAdicionalSabor {
    public function __construct(
        public readonly string $nome,
        public readonly int $quantidade,
    ) {}

    /**
     * @param IngredienteEscolhido[] $adicionais
     * @return DetalhesAdicionalSabor[]
     */
    public static function criarComEntidades(array $adicionais): array {
        $quantidadesPorNome = [];
        foreach ($adicionais as $adicional) {
            $nome = $adicional->ingrediente->nome;
            $quantidadesPorNome[$nome] = ($quantidadesPorNome[$nome] ?? 0) + 1;
        }

        $detalhesAdicionais = [];
        foreach ($quantidadesPorNome as $nome => $quantidade) {
            $detalhesAdicionais[] = new self(
                nome: (string) $nome,
                quantidade: $quantidade,
            );
        }

        return $detalhesAdicionais;
    }
}
