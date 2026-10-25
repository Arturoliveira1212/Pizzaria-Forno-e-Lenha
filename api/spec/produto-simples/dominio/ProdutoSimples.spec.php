<?php

describe('ProdutoSimples', function () {

    describe('temEstoqueSuficiente()', function () {

        it('retorna true quando consumo é menor que o estoque', function () {
            $estoque = 5;
            $produtoSimples = ProdutoSimplesFactory::cocaCola($estoque);
            $consumo = new QuantidadeMedida($estoque - 1, UnidadeMedidaFactory::unidade());

            expect($produtoSimples->temEstoqueSuficiente($consumo))->toBe(true);
        });

        it('retorna true quando consumo é igual ao estoque', function () {
            $estoque = 5;
            $produtoSimples = ProdutoSimplesFactory::cocaCola($estoque);
            $consumo = new QuantidadeMedida($estoque, UnidadeMedidaFactory::unidade());

            expect($produtoSimples->temEstoqueSuficiente($consumo))->toBe(true);
        });

        it('retorna false quando consumo é maior que o estoque', function () {
            $estoque = 10;
            $produtoSimples = ProdutoSimplesFactory::cocaCola($estoque);
            $consumo = new QuantidadeMedida($estoque + 1, UnidadeMedidaFactory::unidade());

            expect($produtoSimples->temEstoqueSuficiente($consumo))->toBe(false);
        });

    });

});
