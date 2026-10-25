<?php

describe('Ingrediente', function () {

    describe('temEstoqueSuficiente()', function () {

        it('retorna true quando consumo é menor que o estoque', function () {
            $estoque = 5;
            $ingrediente = IngredienteFactory::presunto($estoque);
            $consumo = new QuantidadeMedida($estoque - 1, UnidadeMedidaFactory::gramas());

            expect($ingrediente->temEstoqueSuficiente($consumo))->toBe(true);
        });

        it('retorna true quando consumo é igual ao estoque', function () {
            $estoque = 5;
            $ingrediente = IngredienteFactory::manjericao($estoque);
            $consumo = new QuantidadeMedida($estoque, UnidadeMedidaFactory::folhas());

            expect($ingrediente->temEstoqueSuficiente($consumo))->toBe(true);
        });

        it('retorna false quando consumo é maior que o estoque', function () {
            $estoque = 5;
            $ingrediente = IngredienteFactory::molho($estoque);
            $consumo = new QuantidadeMedida($estoque + 1, UnidadeMedidaFactory::litros());

            expect($ingrediente->temEstoqueSuficiente($consumo))->toBe(false);
        });

    });

    describe('copiarComEstoque()', function () {

        it('retorna uma nova instância de Ingrediente com o estoque atualizado', function () {
            $estoqueOriginal = 5;
            $ingrediente = IngredienteFactory::bacon($estoqueOriginal);
            $novoEstoque = new QuantidadeMedida(10, UnidadeMedidaFactory::unidade());

            $ingredienteAtualizado = $ingrediente->copiarComEstoque($novoEstoque);

            expect($ingredienteAtualizado)->toBeAnInstanceOf(Ingrediente::class);
            expect($ingredienteAtualizado->id)->toBe($ingrediente->id);
            expect($ingredienteAtualizado->nome)->toBe($ingrediente->nome);
            expect($ingredienteAtualizado->estoque)->toEqual($novoEstoque);
        });

    });

});
