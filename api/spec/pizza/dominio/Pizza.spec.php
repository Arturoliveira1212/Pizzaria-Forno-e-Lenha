<?php

describe('Pizza', function () {

    beforeAll(function () {
        $this->molho = IngredienteFactory::molho();
        $this->mussarela = IngredienteFactory::mussarela();
        $this->cebola = IngredienteFactory::cebola();
        $this->bacon = IngredienteFactory::bacon();
        $this->pizzaPortuguesa = PizzaFactory::portuguesa();
    });

    describe('obterAdicional()', function () {

        it('retorna o adicional quando ele existe', function () {
            $adicional = $this->pizzaPortuguesa->obterAdicional($this->bacon);

            expect($adicional)->toBeAnInstanceOf(IngredienteAdicional::class);
            expect($adicional->ingrediente->id)->toBe($this->bacon->id);
        });

        it('retorna null quando o adicional não existe', function () {
            $adicional = $this->pizzaPortuguesa->obterAdicional($this->cebola);

            expect($adicional)->toBeNull();
        });

    });

    describe('temComoIngredientePadrao()', function () {

        it('retorna true quando o ingrediente é padrão', function () {
            expect($this->pizzaPortuguesa->temComoIngredientePadrao($this->molho))->toBe(true);
        });

        it('retorna false quando o ingrediente não é padrão', function () {
            expect($this->pizzaPortuguesa->temComoIngredientePadrao($this->cebola))->toBe(false);
        });

    });

    describe('temComoIngredienteSubstituto()', function () {

        it('retorna true quando o ingrediente é substituto', function () {
            expect($this->pizzaPortuguesa->temComoIngredienteSubstituto($this->cebola))->toBe(true);
        });

        it('retorna false quando o ingrediente não é substituto', function () {
            expect($this->pizzaPortuguesa->temComoIngredienteSubstituto($this->molho))->toBe(false);
        });

    });

    describe('obterIdsIngredientesPadraoNaoPersonalizaveis()', function () {

        it('retorna os ids dos ingredientes padrão que não são personalizáveis', function () {
            $idsIngredientesNaoPersonalizaveis = $this->pizzaPortuguesa->obterIdsIngredientesPadraoNaoPersonalizaveis();

            expect($idsIngredientesNaoPersonalizaveis)->toHaveLength(2);
            expect($idsIngredientesNaoPersonalizaveis)->toContain($this->molho->id);
            expect($idsIngredientesNaoPersonalizaveis)->toContain($this->mussarela->id);
        });

    });

    describe('obterPrecoPorTamanho()', function () {

        it('retorna o preço quando ele existe', function () {
            $preco = $this->pizzaPortuguesa->obterPrecoPorTamanho(TamanhoPizza::MEDIA);

            expect($preco->centavos)->toBe(3990);
        });

        it('retorna null quando o preço não existe', function () {
            $preco = $this->pizzaPortuguesa->obterPrecoPorTamanho(TamanhoPizza::GRANDE);

            expect($preco)->toBeNull();
        });

    });

    describe('temTamanho()', function () {

        it('retorna true quando o tamanho existe', function () {
            expect($this->pizzaPortuguesa->temTamanho(TamanhoPizza::MEDIA))->toBe(true);
        });

        it('retorna false quando o tamanho não existe', function () {
            expect($this->pizzaPortuguesa->temTamanho(TamanhoPizza::GRANDE))->toBe(false);
        });

    });

});
