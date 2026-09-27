<?php

describe('Dinheiro', function () {

    describe('validar()', function () {

        it('lança exceção quando o valor em centavos é menor que o valor mínimo permitido', function () {
            expect(function () {
                $centavos = Dinheiro::MENOR_VALOR_PERMITIDO - 1;
                new Dinheiro($centavos);
            })->toThrow(new DominioException('O valor em centavos não pode ser negativo.'));
        });

        it('cria o dinheiro quando todos os campos são válidos', function () {
            expect(new Dinheiro(1500)->centavos)->toBe(1500);
        });

    });

    describe('::zero()', function () {

        it('retorna um dinheiro com zero centavos', function () {
            expect(Dinheiro::zero()->centavos)->toBe(0);
        });

    });

    describe('::emReais()', function () {

        it('converte reais para centavos corretamente', function () {
            expect(Dinheiro::emReais(10.50)->centavos)->toBe(1050);
        });

        it('arredonda corretamente valores com fração de centavo', function () {
            expect(Dinheiro::emReais(10.999)->centavos)->toBe(1100);
            expect(Dinheiro::emReais(10.994)->centavos)->toBe(1099);
        });

    });

    describe('somar()', function () {

        it('soma dois valores corretamente', function () {
            expect(new Dinheiro(500)->somar(new Dinheiro(300))->centavos)->toBe(800);
        });

        it('soma dois valores corretamente sem alterar os objetos originais', function () {
            $a = new Dinheiro(500);
            $b = new Dinheiro(300);
            $a->somar($b);

            expect($a->centavos)->toBe($a->centavos);
            expect($b->centavos)->toBe($b->centavos);
        });

    });

    describe('subtrair()', function () {

        it('subtrai dois valores corretamente', function () {
            expect(new Dinheiro(500)->subtrair(new Dinheiro(300))->centavos)->toBe(200);
        });

        it('subtrai dois valores corretamente sem alterar os objetos originais', function () {
            $a = new Dinheiro(500);
            $b = new Dinheiro(300);
            $a->subtrair($b);

            expect($a->centavos)->toBe($a->centavos);
            expect($b->centavos)->toBe($b->centavos);
        });

        it('não permite que o resultado da subtração seja negativo', function () {
            expect(new Dinheiro(200)->subtrair(new Dinheiro(300))->centavos)->toBe(0);
        });

    });

    describe('multiplicar()', function () {

        it('multiplica por um fator inteiro', function () {
            expect(new Dinheiro(500)->multiplicar(3)->centavos)->toBe(1500);
        });

        it('multiplica por um fator flutuante', function () {
            expect(new Dinheiro(333)->multiplicar(1.5)->centavos)->toBe(500);
        });

        it('lança exceção ao multiplicar por número não positivo', function () {
            expect(function () {
                new Dinheiro(500)->multiplicar(-1);
            })->toThrow(new DominioException('Não é possível multiplicar por número não positivo.'));
        });

    });

    describe('dividir()', function () {

        it('divide por um fator inteiro', function () {
            expect(new Dinheiro(1000)->dividir(4)->centavos)->toBe(250);
        });

        it('divide por um fator flutuante', function () {
            expect(new Dinheiro(1000)->dividir(3.2)->centavos)->toBe(313);
        });

        it('lança exceção ao dividir por número não positivo', function () {
            expect(function () {
                new Dinheiro(1000)->dividir(0);
            })->toThrow(new DominioException('Não é possível dividir por número não positivo.'));
        });

    });

    describe('ehIgualA()', function () {

        it('retorna true quando os valores são iguais', function () {
            expect(new Dinheiro(500)->ehIgualA(new Dinheiro(500)))->toBe(true);
        });

        it('retorna false quando os valores são diferentes', function () {
            expect(new Dinheiro(500)->ehIgualA(new Dinheiro(600)))->toBe(false);
        });

    });

    describe('ehMenorQue()', function () {

        it('retorna true quando o valor é menor', function () {
            expect(new Dinheiro(400)->ehMenorQue(new Dinheiro(500)))->toBe(true);
        });

        it('retorna false quando o valor é igual ou maior', function () {
            expect(new Dinheiro(500)->ehMenorQue(new Dinheiro(500)))->toBe(false);
            expect(new Dinheiro(600)->ehMenorQue(new Dinheiro(500)))->toBe(false);
        });

    });

    describe('ehMenorOuIgualA()', function () {

        it('retorna true quando o valor é menor ou igual', function () {
            expect(new Dinheiro(400)->ehMenorOuIgualA(new Dinheiro(500)))->toBe(true);
            expect(new Dinheiro(500)->ehMenorOuIgualA(new Dinheiro(500)))->toBe(true);
        });

        it('retorna false quando o valor é maior', function () {
            expect(new Dinheiro(600)->ehMenorOuIgualA(new Dinheiro(500)))->toBe(false);
        });

    });

    describe('ehMaiorQue()', function () {

        it('retorna true quando o valor é maior', function () {
            expect(new Dinheiro(600)->ehMaiorQue(new Dinheiro(500)))->toBe(true);
        });

        it('retorna false quando o valor é igual ou menor', function () {
            expect(new Dinheiro(500)->ehMaiorQue(new Dinheiro(500)))->toBe(false);
            expect(new Dinheiro(400)->ehMaiorQue(new Dinheiro(500)))->toBe(false);
        });

    });

    describe('ehMaiorOuIgualA()', function () {

        it('retorna true quando o valor é maior ou igual', function () {
            expect(new Dinheiro(600)->ehMaiorOuIgualA(new Dinheiro(500)))->toBe(true);
            expect(new Dinheiro(500)->ehMaiorOuIgualA(new Dinheiro(500)))->toBe(true);
        });

        it('retorna false quando o valor é menor', function () {
            expect(new Dinheiro(400)->ehMaiorOuIgualA(new Dinheiro(500)))->toBe(false);
        });

    });

    describe('formatar()', function () {

        it('formata valores simples corretamente', function () {
            expect(new Dinheiro(1050)->formatar())->toBe('R$ 10,50');
        });

        it('formata valores com milhar corretamente', function () {
            expect(new Dinheiro(1234567)->formatar())->toBe('R$ 12.345,67');
        });

    });

});
