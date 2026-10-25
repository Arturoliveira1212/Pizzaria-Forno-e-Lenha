<?php

describe('ItemSimples', function () {

    describe('validar()', function () {

        beforeAll(function () {
            $this->produtoSimples = ProdutoSimplesFactory::cocaCola();
        });

        it('lança exceção quando o identificador é menor que o mínimo permitido', function () {
            expect(function () {
                $id = ItemPedido::MENOR_ID_PERMITIDO - 1;

                return new ItemSimples(
                    id: $id,
                    quantidade: ItemPedido::QUANTIDADE_MINIMA_PERMITIDA,
                    produtoSimples: $this->produtoSimples,
                );
            })->toThrow(new DominioException('O identificador do ingrediente escolhido deve ser maior que ' . ItemPedido::MENOR_ID_PERMITIDO . '.'));
        });

        it('lança exceção quando a quantidade é menor que o mínimo permitido', function () {
            expect(function () {
                $quantidade = ItemPedido::QUANTIDADE_MINIMA_PERMITIDA - 1;

                return new ItemSimples(
                    id: ItemPedido::MENOR_ID_PERMITIDO,
                    quantidade: $quantidade,
                    produtoSimples: $this->produtoSimples,
                );
            })->toThrow(new DominioException('A quantidade deve ser maior ou igual a ' . ItemPedido::QUANTIDADE_MINIMA_PERMITIDA . '.'));
        });

        it('cria item simples quando todos os campos são válidos', function () {
            $quantidade = 3;

            $itemSimples = new ItemSimples(
                id: ItemPedido::MENOR_ID_PERMITIDO,
                quantidade: $quantidade,
                produtoSimples: $this->produtoSimples,
            );

            expect($itemSimples->id)->toBe(ItemPedido::MENOR_ID_PERMITIDO);
            expect($itemSimples->quantidade)->toBe($quantidade);
            expect($itemSimples->precoPraticado->centavos)->toBe(590);
        });
    });

    describe('calcularPrecoTotal()', function () {

        beforeAll(function () {
            $this->produtoSimples = ProdutoSimplesFactory::sucoLaranja();
        });

        it('calcula o preço total corretamente (1 quantidade)', function () {
            $itemSimples = new ItemSimples(
                id: ItemPedido::MENOR_ID_PERMITIDO,
                quantidade: 1,
                produtoSimples: $this->produtoSimples,
            );

            expect($itemSimples->calcularPrecoTotal()->centavos)->toBe(998);
        });

        it('calcula o preço total corretamente (3 quantidades)', function () {
            $itemSimples = new ItemSimples(
                id: ItemPedido::MENOR_ID_PERMITIDO,
                quantidade: 3,
                produtoSimples: $this->produtoSimples,
            );

            expect($itemSimples->calcularPrecoTotal()->centavos)->toBe(2994);
        });
    });

    describe('obterConsumo()', function () {

        it('retorna a o consumo corretamente', function () {
            $produtoSimples = ProdutoSimplesFactory::cocaCola();
            $quantidade = 5;

            $itemSimples = new ItemSimples(
                id: ItemPedido::MENOR_ID_PERMITIDO,
                quantidade: $quantidade,
                produtoSimples: ProdutoSimplesFactory::cocaCola(),
            );

            $consumo = $itemSimples->obterConsumo();

            expect($consumo->valor)->toBe($quantidade);
            expect($consumo->unidadeMedida->id)->toBe($produtoSimples->estoque->unidadeMedida->id);
        });
    });

    describe('copiarComQuantidade()', function () {

        it('cria uma cópia do item simples com a nova quantidade', function () {
            $quantidadeOriginal = 2;
            $novaQuantidade = 4;

            $itemSimplesOriginal = new ItemSimples(
                id: ItemPedido::MENOR_ID_PERMITIDO,
                quantidade: $quantidadeOriginal,
                produtoSimples: ProdutoSimplesFactory::cocaCola(),
            );

            $itemSimplesCopia = $itemSimplesOriginal->copiarComQuantidade($novaQuantidade);

            expect($itemSimplesCopia->id)->toBe($itemSimplesOriginal->id);
            expect($itemSimplesCopia->quantidade)->toBe($novaQuantidade);
            expect($itemSimplesCopia->produtoSimples)->toBe($itemSimplesOriginal->produtoSimples);
        });
    });
});
