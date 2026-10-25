<?php

describe('QuantidadeMedida', function () {

    describe('validar()', function () {

        it('lança exceção quando o valor é menor que o valor mínimo permitido', function () {
            expect(function () {
                $valor = QuantidadeMedida::MENOR_VALOR_PERMITIDO - 1;
                new QuantidadeMedida($valor, UnidadeMedidaFactory::gramas());
            })->toThrow(new DominioException('O valor não pode ser negativo.'));
        });

        it('cria quantidade medida quando todos os campos são válidos', function () {
            expect(function () {
                $valor = QuantidadeMedida::MENOR_VALOR_PERMITIDO;
                new QuantidadeMedida($valor, UnidadeMedidaFactory::gramas());
            })->not->toThrow();
        });

    });

    describe('validarOperacao()', function () {

        it('lança exceção quando as unidades de medida são diferentes', function () {
            $quantidadeMedida1 = new QuantidadeMedida(5, UnidadeMedidaFactory::gramas());
            $quantidadeMedida2 = new QuantidadeMedida(5, UnidadeMedidaFactory::litros());

            expect(function () use ($quantidadeMedida1, $quantidadeMedida2) {
                $quantidadeMedida1->ehMenorOuIgualA($quantidadeMedida2);
            })->toThrow(new DominioException('As unidades de medida devem ser iguais para comparar quantidades.'));
        });

        it('não lança exceção quando as unidades de medida são iguais', function () {
            $quantidadeMedida1 = new QuantidadeMedida(5, UnidadeMedidaFactory::gramas());
            $quantidadeMedida2 = new QuantidadeMedida(5, UnidadeMedidaFactory::gramas());

            expect(function () use ($quantidadeMedida1, $quantidadeMedida2) {
                $quantidadeMedida1->ehMenorOuIgualA($quantidadeMedida2);
            })->not->toThrow();
        });

    });

    describe('ehMenorOuIgualA()', function () {

        it('retorna true quando o valor é menor', function () {
            $quantidadeMedida1 = new QuantidadeMedida(5, UnidadeMedidaFactory::gramas());
            $quantidadeMedida2 = new QuantidadeMedida(6, UnidadeMedidaFactory::gramas());

            expect($quantidadeMedida1->ehMenorOuIgualA($quantidadeMedida2))->toBe(true);
        });

        it('retorna true quando o valor é igual', function () {
            $quantidadeMedida1 = new QuantidadeMedida(5, UnidadeMedidaFactory::gramas());
            $quantidadeMedida2 = new QuantidadeMedida(5, UnidadeMedidaFactory::gramas());

            expect($quantidadeMedida1->ehMenorOuIgualA($quantidadeMedida2))->toBe(true);
        });

        it('retorna false quando o valor é maior', function () {
            $quantidadeMedida1 = new QuantidadeMedida(6, UnidadeMedidaFactory::gramas());
            $quantidadeMedida2 = new QuantidadeMedida(5, UnidadeMedidaFactory::gramas());

            expect($quantidadeMedida1->ehMenorOuIgualA($quantidadeMedida2))->toBe(false);
        });

    });

    describe('ehMaiorQueZero()', function () {

        it('retorna true quando o valor é maior que zero', function () {
            $quantidadeMedida = new QuantidadeMedida(5, UnidadeMedidaFactory::gramas());

            expect($quantidadeMedida->ehMaiorQueZero())->toBe(true);
        });

        it('retorna false quando o valor é igual a zero', function () {
            $quantidadeMedida = new QuantidadeMedida(0, UnidadeMedidaFactory::gramas());

            expect($quantidadeMedida->ehMaiorQueZero())->toBe(false);
        });

    });

    describe('subtrair()', function () {

        it('subtrai uma quantidade de medida de outra', function () {
            $quantidadeMedida1 = new QuantidadeMedida(10, UnidadeMedidaFactory::gramas());
            $quantidadeMedida2 = new QuantidadeMedida(5, UnidadeMedidaFactory::gramas());

            $resultado = $quantidadeMedida1->subtrair($quantidadeMedida2);

            expect($resultado->valor)->toBe(5);
            expect($resultado->unidadeMedida->id)->toBe($quantidadeMedida1->unidadeMedida->id);
        });

        it('não permite que a subtração seja negativo', function () {
            $quantidadeMedida1 = new QuantidadeMedida(5, UnidadeMedidaFactory::gramas());
            $quantidadeMedida2 = new QuantidadeMedida(10, UnidadeMedidaFactory::gramas());

            $resultado = $quantidadeMedida1->subtrair($quantidadeMedida2);

            expect($resultado->valor)->toBe(0);
            expect($resultado->unidadeMedida->id)->toBe($quantidadeMedida1->unidadeMedida->id);
        });

    });

    describe('dividir()', function () {

        it('divide uma quantidade de medida por um número', function () {
            $quantidadeMedida = new QuantidadeMedida(10, UnidadeMedidaFactory::gramas());

            $resultado = $quantidadeMedida->dividir(3);

            expect($resultado->valor)->toBe(3);
            expect($resultado->unidadeMedida->id)->toBe($quantidadeMedida->unidadeMedida->id);
        });

        it('lança exceção quando o divisor é menor ou igual a zero', function () {
            $quantidadeMedida = new QuantidadeMedida(10, UnidadeMedidaFactory::gramas());

            expect(function () use ($quantidadeMedida) {
                $quantidadeMedida->dividir(0);
            })->toThrow(new DominioException('Não é possível dividir por número não positivo.'));
        });

    });

});
