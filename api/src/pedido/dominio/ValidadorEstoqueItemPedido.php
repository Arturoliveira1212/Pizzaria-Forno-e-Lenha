<?php

class ValidadorEstoqueItemPedido {
    /**
     * @param ItemSimples[] $itensSimples
     * @param string[] $erros
     * @return ItemSimples[]
     */
    public function validarEstoqueItensSimples(array $itensSimples, array &$erros): array {
        $itensSimplesAtualizados = [];

        foreach ($itensSimples as $itemSimples) {
            $item = $this->validarEstoqueItemSimples(itemSimples: $itemSimples, erros: $erros);
            if (!$item instanceof ItemSimples) {
                continue;
            }

            $itensSimplesAtualizados[] = $item;
        }

        return $itensSimplesAtualizados;
    }

    /**
     * @param string[] $erros
     * @return ItemSimples|null
     */
    private function validarEstoqueItemSimples(ItemSimples $itemSimples, array &$erros): ?ItemSimples {
        $produtoSimples = $itemSimples->produtoSimples;
        $idProdutoSimples = $produtoSimples->id;

        $consumo = $itemSimples->obterConsumo();
        if (!$produtoSimples->temEstoqueSuficiente($consumo)) {
            $estoqueAtual = $produtoSimples->estoque;
            if ($estoqueAtual->ehMaiorQueZero()) {
                $erros[] = "Produto {$produtoSimples->nome} (ID {$idProdutoSimples}) não possui estoque suficiente. Quantidade ajustada para {$estoqueAtual->valor}.";

                return $itemSimples->copiarComQuantidade($estoqueAtual->valor);
            }

            $erros[] = "Produto {$produtoSimples->nome} (ID {$idProdutoSimples}) está sem estoque.";

            return null;
        }

        return $itemSimples;
    }

    /**
     * @param ItemPizza[] $itensPizza
     * @param string[] $erros
     * @return ItemPizza[]
     */
    public function validarEstoqueItensPizza(array $itensPizza, array &$erros): array {
        $itensPizzaAtualizados = [];
        $mapaIngredientes = [];

        foreach ($itensPizza as $itemPizza) {
            $item = $this->validarEstoqueItemPizza(itemPizza: $itemPizza, mapaIngredientes: $mapaIngredientes, erros: $erros);
            if (!$item instanceof ItemPizza) {
                continue;
            }

            $itensPizzaAtualizados[] = $item;
        }

        return $itensPizzaAtualizados;
    }

    /**
     * @param array<int, Ingrediente> $mapaIngredientes
     * @param string[] $erros
     * @return ItemPizza|null
     */
    private function validarEstoqueItemPizza(ItemPizza $itemPizza, array &$mapaIngredientes, array &$erros): ?ItemPizza {
        $saboresEscolhidos = $itemPizza->saboresEscolhidos;
        $quantidadeSabores = count($saboresEscolhidos);
        $inconsistenciasEncontradas = false;

        foreach ($saboresEscolhidos as $saborEscolhido) {
            $ingredientesEscolhidos = $saborEscolhido->ingredientesEscolhidos;

            foreach ($ingredientesEscolhidos as $ingredienteEscolhido) {
                $consumo = $saborEscolhido->obterConsumo(
                    ingredienteEscolhido: $ingredienteEscolhido,
                    tamanhoPizza: $itemPizza->tamanhoPizza,
                    quantidadeSabores: $quantidadeSabores,
                    quantidadeItem: $itemPizza->quantidade,
                );

                $ingrediente = $ingredienteEscolhido->ingrediente;
                if (!isset($mapaIngredientes[$ingrediente->id])) {
                    $mapaIngredientes[$ingrediente->id] = $ingrediente;
                }

                $ingredienteAtualizado = $mapaIngredientes[$ingrediente->id];
                if (!$ingredienteAtualizado->temEstoqueSuficiente($consumo)) {
                    $erros[] = "Ingrediente {$ingrediente->nome} (ID {$ingrediente->id}) não possui estoque suficiente.";
                    $inconsistenciasEncontradas = true;
                    continue;
                }

                $novoEstoque = $ingredienteAtualizado->estoque->subtrair($consumo);
                $mapaIngredientes[$ingrediente->id] = $ingredienteAtualizado->copiarComEstoque($novoEstoque);
            }
        }

        if ($inconsistenciasEncontradas) {
            return null;
        }

        return $itemPizza;
    }
}
