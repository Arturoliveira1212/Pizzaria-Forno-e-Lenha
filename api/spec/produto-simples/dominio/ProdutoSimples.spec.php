<?php

describe('ProdutoSimples', function () {

    describe('temEstoqueSuficiente()', function () {

        it('retorna true quando a quantidade medida é menor que o estoque', function () {
            $estoque = 5;
            $produtoSimples = ProdutoSimplesFactory::cocaCola($estoque);
            $quantidadeSolicitada = new QuantidadeMedida($estoque - 1, UnidadeMedidaFactory::unidade());

            expect($produtoSimples->temEstoqueSuficiente($quantidadeSolicitada))->toBe(true);
        });

        it('retorna true quando a quantidade medida é igual ao estoque', function () {
            $estoque = 5;
            $produtoSimples = ProdutoSimplesFactory::cocaCola($estoque);
            $quantidadeSolicitada = new QuantidadeMedida($estoque, UnidadeMedidaFactory::unidade());

            expect($produtoSimples->temEstoqueSuficiente($quantidadeSolicitada))->toBe(true);
        });

        it('retorna false quando a quantidade medida é maior que o estoque', function () {
            $estoque = 10;
            $produtoSimples = ProdutoSimplesFactory::cocaCola($estoque);
            $quantidadeSolicitada = new QuantidadeMedida($estoque + 1, UnidadeMedidaFactory::unidade());

            expect($produtoSimples->temEstoqueSuficiente($quantidadeSolicitada))->toBe(false);
        });

    });

});
