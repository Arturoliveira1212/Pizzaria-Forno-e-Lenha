<?php

describe('DescontoPedido', function () {

    describe('validar()', function () {

        it('lança exceção quando o identificador do desconto é menor que o mínimo permitido', function () {
            expect(function () {
                new DescontoFidelidade(
                    id: DescontoPedido::MENOR_ID_PERMITIDO - 1,
                    valorDesconto: new Dinheiro(10),
                    beneficioFidelidade: new BeneficioFidelidade(
                        id: 1,
                        percentualDesconto: new Percentual(10),
                        dataConcessao: new DateTime('2024-01-01'),
                        validade: new DateTime('2024-12-31'),
                        utilizado: false,
                    ),
                );
            })->toThrow(new DominioException('O identificador do ingrediente escolhido deve ser maior que ' . DescontoPedido::MENOR_ID_PERMITIDO . '.'));
        });

        it('lança exceção quando o valor do desconto é zero', function () {
            expect(function () {
                new DescontoPromocao(
                    id: DescontoPedido::MENOR_ID_PERMITIDO,
                    valorDesconto: Dinheiro::zero(),
                    promocao: new Promocao(
                        id: 1,
                        descricao: 'Descrição Teste',
                        quantidadeMinima: 1,
                        tamanhoPizza: TamanhoPizza::MEDIA,
                        percentualDesconto: new Percentual(10),
                        dataInicio: new DateTime('2024-01-01'),
                        dataFim: new DateTime('2024-12-31'),
                    ),
                );
            })->toThrow(new DominioException('O valor do desconto não pode ser zero.'));
        });

        it('cria o desconto quando todos os campos são válidos', function () {
            $desconto = new DescontoFidelidade(
                id: DescontoPedido::MENOR_ID_PERMITIDO,
                valorDesconto: new Dinheiro(10),
                beneficioFidelidade: new BeneficioFidelidade(
                    id: 1,
                    percentualDesconto: new Percentual(10),
                    dataConcessao: new DateTime('2024-01-01'),
                    validade: new DateTime('2024-12-31'),
                    utilizado: false,
                ),
            );

            expect($desconto->id)->toBe(DescontoPedido::MENOR_ID_PERMITIDO);
            expect($desconto->valorDesconto->centavos)->toBe(10);
        });

    });

});
