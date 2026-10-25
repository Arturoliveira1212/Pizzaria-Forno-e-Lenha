<?php

describe('IngredienteSubstituto', function () {

    beforeAll(function () {
        $this->ingredienteSubstituto = IngredienteSubstitutoFactory::presunto();
    });

    describe('obterQuantidadePorTamanho()', function () {

        it('retorna a quantidade por tamanho quando ela existe', function () {
            $quantidadePorTamanho = $this->ingredienteSubstituto->obterQuantidadePorTamanho(TamanhoPizza::MEDIA);

            expect($quantidadePorTamanho)->toBeAnInstanceOf(QuantidadePorTamanho::class);
            expect($quantidadePorTamanho->tamanhoPizza)->toBe(TamanhoPizza::MEDIA);
        });

        it('retorna null quando a quantidade por tamanho não existe', function () {
            $quantidadePorTamanho = $this->ingredienteSubstituto->obterQuantidadePorTamanho(TamanhoPizza::GRANDE);

            expect($quantidadePorTamanho)->toBeNull();
        });

    });

});
