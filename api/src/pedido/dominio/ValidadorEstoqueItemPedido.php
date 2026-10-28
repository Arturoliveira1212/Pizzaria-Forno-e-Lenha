<?php

class ValidadorEstoqueItemPedido {
    /**
     * @param ItemPedido[] $itensPedido
     * @param string[] $erros
     */
    public function validarEstoqueItensPedido(array $itensPedido, array &$erros): void {
        $mapaIngredientes = [];

        foreach ($itensPedido as $itemPedido) {
            if ($itemPedido instanceof ItemPizza) {
                $this->validarEstoqueItemPizza(
                    itemPizza: $itemPedido,
                    mapaIngredientes: $mapaIngredientes,
                    erros: $erros,
                );
                continue;
            }

            if ($itemPedido instanceof ItemSimples) {
                $this->validarEstoqueItemSimples(itemSimples: $itemPedido, erros: $erros);
                continue;
            }
        }

    }

    /**
     * @param ItemSimples $itemSimples
     * @param string[] $erros
     */
    private function validarEstoqueItemSimples(ItemSimples $itemSimples, array &$erros): void {
        $produtoSimples = $itemSimples->produtoSimples;
        $idProdutoSimples = $produtoSimples->id;

        $consumo = $itemSimples->obterConsumo();
        if (!$produtoSimples->temEstoqueSuficiente($consumo)) {
            $estoqueAtual = $produtoSimples->estoque;
            if ($estoqueAtual->ehMaiorQueZero()) {
                $erros[] = "Produto {$produtoSimples->nome} (ID {$idProdutoSimples}) não possui estoque suficiente. Quantidade em estoque: {$estoqueAtual->valor}.";

                return;
            }

            $erros[] = "Produto {$produtoSimples->nome} (ID {$idProdutoSimples}) está sem estoque. Retire o produto do pedido.";
        }
    }

    /**
     * @param ItemPizza $itemPizza
     * @param array<int, Ingrediente> $mapaIngredientes
     * @param string[] $erros
     */
    private function validarEstoqueItemPizza(ItemPizza $itemPizza, array &$mapaIngredientes, array &$erros): void {
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
                    $inconsistenciasEncontradas = true;
                    continue;
                }

                $novoEstoque = $ingredienteAtualizado->estoque->subtrair($consumo);
                $mapaIngredientes[$ingrediente->id] = $ingredienteAtualizado->copiarComEstoque($novoEstoque);
            }
        }

        if ($inconsistenciasEncontradas) {
            $erros[] = "{$itemPizza->obterDescricaoSabores()} não possui estoque suficiente. Retire ou atualize a quantidade da pizza no pedido.";
        }
    }
}
