<?php

class CalculadoraDescontoPromocao {
    /**
     * @param ItemPizza[] $itensPizza
     * @param Promocao[] $promocoes
     * @return DescontoPromocao[]
     */
    public function calcularDescontos(array $itensPizza, array $promocoes): array {
        if (empty($promocoes)) {
            return [];
        }

        $mapa = $this->obterMapaDePizzasPorTamanho($itensPizza);

        $descontosPromocao = [];

        foreach ($promocoes as $promocao) {
            $quantidadeTotal = $mapa[$promocao->tamanhoPizza->value]['quantidadeTotal'] ?? 0;
            if ($quantidadeTotal < $promocao->quantidadeMinima) {
                continue;
            }

            $valorTotal = $mapa[$promocao->tamanhoPizza->value]['valorTotal'];
            $valorDesconto = $promocao->percentualDesconto->calcularSobre($valorTotal);

            $descontosPromocao[] = new DescontoPromocao(
                id: 0,
                promocao: $promocao,
                valorDesconto: $valorDesconto,
            );
        }

        return $descontosPromocao;
    }

    /**
     * @param ItemPizza[] $itensPizza
     * @return array<string, array{quantidadeTotal: int, valorTotal: Dinheiro}>
     */
    private function obterMapaDePizzasPorTamanho(array $itensPizza): array {
        $mapa = [];

        foreach ($itensPizza as $itemPizza) {
            $tamanho = $itemPizza->tamanhoPizza;
            if (!isset($mapa[$tamanho->value])) {
                $mapa[$tamanho->value] = [
                    'quantidadeTotal' => 0,
                    'valorTotal' => Dinheiro::zero(),
                ];
            }

            $mapa[$tamanho->value] = [
                'quantidadeTotal' => $mapa[$tamanho->value]['quantidadeTotal'] + $itemPizza->quantidade,
                'valorTotal' => $mapa[$tamanho->value]['valorTotal']->somar($itemPizza->calcularPrecoTotal()),
            ];
        }

        return $mapa;
    }
}
