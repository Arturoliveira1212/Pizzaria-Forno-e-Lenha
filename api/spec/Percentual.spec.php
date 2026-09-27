<?php

describe('Percentual', function () {

    describe('validar()', function () {

        it('lança exceção quando o valor é menor que o mínimo permitido', function () {
            expect(function () {
                $valor = Percentual::MENOR_VALOR_PERMITIDO - 1;
                new Percentual($valor);
            })->toThrow(new DominioException('O valor do percentual deve estar entre ' . Percentual::MENOR_VALOR_PERMITIDO . ' e ' . Percentual::MAIOR_VALOR_PERMITIDO . '.'));
        });

        it('lança exceção quando o valor é maior que o máximo permitido', function () {
            expect(function () {
                $valor = Percentual::MAIOR_VALOR_PERMITIDO + 1;
                new Percentual($valor);
            })->toThrow(new DominioException('O valor do percentual deve estar entre ' . Percentual::MENOR_VALOR_PERMITIDO . ' e ' . Percentual::MAIOR_VALOR_PERMITIDO . '.'));
        });

        it('cria o percentual quando todos os campos são válidos', function () {
            expect(new Percentual(40)->valor)->toBe(40);
        });

    });

    describe('calcularSobre()', function () {

        it('calcula corretamente o percentual sobre um valor', function () {
            $percentual = new Percentual(7);
            $valor = new Dinheiro(7987);

            expect($percentual->calcularSobre($valor)->centavos)->toBe(559);
        });

    });

    describe('somarSobre()', function () {

        it('soma corretamente o percentual sobre um valor', function () {
            $percentual = new Percentual(6);
            $valor = new Dinheiro(8529);

            expect($percentual->somarSobre($valor)->centavos)->toBe(9041);
        });

    });

    describe('subtrairDe()', function () {

        it('subtrai corretamente o percentual de um valor', function () {
            $percentual = new Percentual(3);
            $valor = new Dinheiro(985);

            expect($percentual->subtrairDe($valor)->centavos)->toBe(955);
        });

    });

});
