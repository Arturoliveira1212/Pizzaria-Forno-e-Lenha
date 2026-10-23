<?php

describe('IngredienteEscolhido', function () {

    describe('validar()', function () {

        $this->ingrediente = IngredienteFactory::mussarela();

        it('lança exceção quando o identificador é menor que o mínimo permitido', function () {
            expect(function () {
                $id = IngredienteEscolhido::MENOR_ID_PERMITIDO - 1;

                return new IngredienteEscolhido(
                    id: $id,
                    ingrediente: $this->ingrediente,
                    papel: PapelIngrediente::PADRAO,
                    custoAdicional: Dinheiro::zero(),
                );
            })->toThrow(new DominioException('O identificador do ingrediente escolhido deve ser maior que ' . IngredienteEscolhido::MENOR_ID_PERMITIDO . '.'));
        });

        it('lança exceção quando o ingrediente escolhido é padrão e o custo adicional é maior que zero', function () {
            expect(function () {
                return new IngredienteEscolhido(
                    id: IngredienteEscolhido::MENOR_ID_PERMITIDO,
                    ingrediente: $this->ingrediente,
                    papel: PapelIngrediente::PADRAO,
                    custoAdicional: new Dinheiro(500),
                );
            })->toThrow(new DominioException('O custo do ingrediente escolhido não pode ser maior que zero.'));
        });

        it('lança exceção quando o ingrediente escolhido é substituto e o custo adicional é maior que zero', function () {
            expect(function () {
                return new IngredienteEscolhido(
                    id: IngredienteEscolhido::MENOR_ID_PERMITIDO,
                    ingrediente: $this->ingrediente,
                    papel: PapelIngrediente::SUBSTITUTO,
                    custoAdicional: new Dinheiro(500),
                );
            })->toThrow(new DominioException('O custo do ingrediente escolhido não pode ser maior que zero.'));
        });

        it('lança exceção quando o ingrediente escolhido é adicional e o custo adicional é igual a zero', function () {
            expect(function () {
                return new IngredienteEscolhido(
                    id: IngredienteEscolhido::MENOR_ID_PERMITIDO,
                    ingrediente: $this->ingrediente,
                    papel: PapelIngrediente::ADICIONAL,
                    custoAdicional: Dinheiro::zero(),
                );
            })->toThrow(new DominioException('O custo do ingrediente adicional não pode ser igual a zero.'));
        });

        it('cria o ingrediente escolhido padrão quando todos os campos são válidos', function () {
            $id = IngredienteEscolhido::MENOR_ID_PERMITIDO;
            $papel = PapelIngrediente::PADRAO;
            $custoAdicional = Dinheiro::zero();

            $ingredienteEscolhido = new IngredienteEscolhido(
                id: $id,
                ingrediente: $this->ingrediente,
                papel: $papel,
                custoAdicional: $custoAdicional,
            );

            expect($ingredienteEscolhido->id)->toBe($id);
            expect($ingredienteEscolhido->ingrediente)->toBe($this->ingrediente);
            expect($ingredienteEscolhido->papel)->toBe($papel);
            expect($ingredienteEscolhido->custoAdicional)->toBe($custoAdicional);
        });

        it('cria o ingrediente escolhido substituto quando todos os campos são válidos', function () {
            $id = IngredienteEscolhido::MENOR_ID_PERMITIDO;
            $papel = PapelIngrediente::SUBSTITUTO;
            $custoAdicional = Dinheiro::zero();

            $ingredienteEscolhido = new IngredienteEscolhido(
                id: $id,
                ingrediente: $this->ingrediente,
                papel: $papel,
                custoAdicional: $custoAdicional,
            );

            expect($ingredienteEscolhido->id)->toBe($id);
            expect($ingredienteEscolhido->ingrediente)->toBe($this->ingrediente);
            expect($ingredienteEscolhido->papel)->toBe($papel);
            expect($ingredienteEscolhido->custoAdicional)->toBe($custoAdicional);
        });

        it('cria o ingrediente escolhido adicional quando todos os campos são válidos', function () {
            $id = IngredienteEscolhido::MENOR_ID_PERMITIDO;
            $papel = PapelIngrediente::ADICIONAL;
            $custoAdicional = new Dinheiro(500);

            $ingredienteEscolhido = new IngredienteEscolhido(
                id: $id,
                ingrediente: $this->ingrediente,
                papel: $papel,
                custoAdicional: $custoAdicional,
            );

            expect($ingredienteEscolhido->id)->toBe($id);
            expect($ingredienteEscolhido->ingrediente)->toBe($this->ingrediente);
            expect($ingredienteEscolhido->papel)->toBe($papel);
            expect($ingredienteEscolhido->custoAdicional)->toBe($custoAdicional);
        });

    });

});
