<?php

describe('SaborEscolhido', function () {

    describe('validar()', function () {

        $this->pizza = PizzaFactory::portuguesa();

        $this->ingredientesEscolhidosNaoPersonalizaveis = [
            IngredienteEscolhidoFactory::padrao(1, IngredienteFactory::molho()),
            IngredienteEscolhidoFactory::padrao(2, IngredienteFactory::mussarela()),
        ];

        it('lança exceção quando o identificador é menor que o mínimo permitido', function () {
            expect(function () {
                $id = IngredienteEscolhido::MENOR_ID_PERMITIDO - 1;

                return new SaborEscolhido(
                    id: $id,
                    pizza: $this->pizza,
                    ingredientesEscolhidos: $this->ingredientesEscolhidosNaoPersonalizaveis,
                );
            })->toThrow(new DominioException('O identificador do ingrediente escolhido deve ser maior que ' . IngredienteEscolhido::MENOR_ID_PERMITIDO . '.'));
        });

        it('lança exceção quando nenhum ingrediente é escolhido', function () {
            expect(function () {
                return new SaborEscolhido(
                    id: SaborEscolhido::MENOR_ID_PERMITIDO,
                    pizza: $this->pizza,
                    ingredientesEscolhidos: [],
                );
            })->toThrow(new DominioException('É necessário escolher todos os ingredientes padrão da pizza.'));
        });

        it('lança exceção quando o mesmo ingrediente é selecionado mais de uma vez como padrão', function () {
            expect(function () {
                return new SaborEscolhido(
                    id: 1,
                    pizza: $this->pizza,
                    ingredientesEscolhidos: [
                        ...$this->ingredientesEscolhidosNaoPersonalizaveis,
                        IngredienteEscolhidoFactory::padrao(1, IngredienteFactory::molho()),
                    ],
                );
            })->toThrow(new DominioException('O ingrediente Molho(1) já foi selecionado como padrão.'));
        });

        it('lança exceção quando o ingrediente escolhido como padrão não é permitido para a pizza', function () {
            expect(function () {
                return new SaborEscolhido(
                    id: 1,
                    pizza: $this->pizza,
                    ingredientesEscolhidos: [
                        ...$this->ingredientesEscolhidosNaoPersonalizaveis,
                        IngredienteEscolhidoFactory::padrao(1, IngredienteFactory::catupiry()),
                    ],
                );
            })->toThrow(new DominioException('O ingrediente Catupiry(8) não é permitido como padrão para a pizza.'));
        });

        it('lança exceção quando o ingrediente escolhido como substituto não é permitido para a pizza', function () {
            expect(function () {
                return new SaborEscolhido(
                    id: 1,
                    pizza: $this->pizza,
                    ingredientesEscolhidos: [
                        ...$this->ingredientesEscolhidosNaoPersonalizaveis,
                        IngredienteEscolhidoFactory::substituto(1, IngredienteFactory::bacon()),
                    ],
                );
            })->toThrow(new DominioException('O ingrediente Bacon(7) não é permitido como substituto para a pizza.'));
        });

        it('lança exceção quando o ingrediente escolhido como adicional não é permitido para a pizza', function () {
            expect(function () {
                return new SaborEscolhido(
                    id: 1,
                    pizza: $this->pizza,
                    ingredientesEscolhidos: [
                        ...$this->ingredientesEscolhidosNaoPersonalizaveis,
                        IngredienteEscolhidoFactory::adicional(1, IngredienteFactory::cebola(), 500),
                    ],
                );
            })->toThrow(new DominioException('O ingrediente Cebola(5) não é permitido como adicional para a pizza.'));
        });

        it('lança exceção quando o custo adicional do ingrediente escolhido não corresponde ao preço definido para a pizza', function () {
            expect(function () {
                return new SaborEscolhido(
                    id: 1,
                    pizza: $this->pizza,
                    ingredientesEscolhidos: [
                        ...$this->ingredientesEscolhidosNaoPersonalizaveis,
                        IngredienteEscolhidoFactory::adicional(1, IngredienteFactory::bacon(), 500),
                    ],
                );
            })->toThrow(new DominioException('O custo adicional do ingrediente Bacon(7) não corresponde ao preço definido para a pizza.'));
        });

        it('lança exceção quando os ingredientes escolhidos não incluem todos os ingredientes não personalizáveis da pizza', function () {
            expect(function () {
                return new SaborEscolhido(
                    id: 1,
                    pizza: $this->pizza,
                    ingredientesEscolhidos: [
                        IngredienteEscolhidoFactory::padrao(1, IngredienteFactory::molho()),

                    ],
                );
            })->toThrow(new DominioException('É necessário escolher todos os ingredientes padrão da pizza.'));
        });

        it('lança exceção quando existem mais ingredientes substitutos escolhidos do que o permitido para a pizza', function () {
            expect(function () {
                return new SaborEscolhido(
                    id: 1,
                    pizza: $this->pizza,
                    ingredientesEscolhidos: [
                        ...$this->ingredientesEscolhidosNaoPersonalizaveis,
                        IngredienteEscolhidoFactory::substituto(1, IngredienteFactory::cebola()),
                        IngredienteEscolhidoFactory::substituto(2, IngredienteFactory::milho()),
                        IngredienteEscolhidoFactory::substituto(3, IngredienteFactory::milho()),
                    ],
                );
            })->toThrow(new DominioException('A quantidade total de ingredientes padrão e substitutos excede o número de vagas da pizza.'));
        });

        it('lança exceção quando existem mais ingredientes padrão e substitutos escolhidos do que o permitido para a pizza', function () {
            expect(function () {
                return new SaborEscolhido(
                    id: 1,
                    pizza: $this->pizza,
                    ingredientesEscolhidos: [
                        ...$this->ingredientesEscolhidosNaoPersonalizaveis,
                        IngredienteEscolhidoFactory::padrao(1, IngredienteFactory::presunto()),
                        IngredienteEscolhidoFactory::substituto(2, IngredienteFactory::milho()),
                        IngredienteEscolhidoFactory::substituto(3, IngredienteFactory::cebola()),
                    ],
                );
            })->toThrow(new DominioException('A quantidade total de ingredientes padrão e substitutos excede o número de vagas da pizza.'));
        });

        it('cria o sabor escolhido quando todos os campos são válidos', function () {
            $id = SaborEscolhido::MENOR_ID_PERMITIDO;

            $saborEscolhido = new SaborEscolhido(
                id: $id,
                pizza: $this->pizza,
                ingredientesEscolhidos: [
                    ...$this->ingredientesEscolhidosNaoPersonalizaveis,
                    IngredienteEscolhidoFactory::padrao(1, IngredienteFactory::presunto()),
                    IngredienteEscolhidoFactory::padrao(1, IngredienteFactory::azeitona()),
                    IngredienteEscolhidoFactory::adicional(3, IngredienteFactory::bacon(), 899),
                ],
            );

            expect($saborEscolhido->id)->toBe($id);
            expect($saborEscolhido->pizza)->toBe($this->pizza);
            expect($saborEscolhido->ingredientesEscolhidos)->toHaveLength(5);
        });

        it('cria o sabor escolhido quando todos os campos são válidos (com 1 substituto)', function () {
            $id = SaborEscolhido::MENOR_ID_PERMITIDO;

            $saborEscolhido = new SaborEscolhido(
                id: $id,
                pizza: $this->pizza,
                ingredientesEscolhidos: [
                    ...$this->ingredientesEscolhidosNaoPersonalizaveis,
                    IngredienteEscolhidoFactory::padrao(1, IngredienteFactory::presunto()),
                    IngredienteEscolhidoFactory::substituto(2, IngredienteFactory::cebola()),
                    IngredienteEscolhidoFactory::adicional(3, IngredienteFactory::bacon(), 899),
                ],
            );

            expect($saborEscolhido->id)->toBe($id);
            expect($saborEscolhido->pizza)->toBe($this->pizza);
            expect($saborEscolhido->ingredientesEscolhidos)->toHaveLength(5);
        });

        it('cria o sabor escolhido quando todos os campos são válidos (com 2 substitutos)', function () {
            $id = SaborEscolhido::MENOR_ID_PERMITIDO;

            $saborEscolhido = new SaborEscolhido(
                id: $id,
                pizza: $this->pizza,
                ingredientesEscolhidos: [
                    ...$this->ingredientesEscolhidosNaoPersonalizaveis,
                    IngredienteEscolhidoFactory::padrao(1, IngredienteFactory::presunto()),
                    IngredienteEscolhidoFactory::substituto(1, IngredienteFactory::cebola()),
                    IngredienteEscolhidoFactory::adicional(3, IngredienteFactory::bacon(), 899),
                ],
            );

            expect($saborEscolhido->id)->toBe($id);
            expect($saborEscolhido->pizza)->toBe($this->pizza);
            expect($saborEscolhido->ingredientesEscolhidos)->toHaveLength(5);
        });

    });

    describe('calcularPrecoDosAdicionais()', function () {

        $this->pizza = PizzaFactory::portuguesa();

        $this->ingredientesEscolhidosPadrao = [
            IngredienteEscolhidoFactory::padrao(1, IngredienteFactory::molho()),
            IngredienteEscolhidoFactory::padrao(2, IngredienteFactory::mussarela()),
        ];

        it('calcula o preço dos adicionais corretamente (0 adicionais)', function () {
            $saborEscolhido = new SaborEscolhido(
                id: SaborEscolhido::MENOR_ID_PERMITIDO,
                pizza: $this->pizza,
                ingredientesEscolhidos: [
                    ...$this->ingredientesEscolhidosPadrao,
                ],
            );

            expect($saborEscolhido->calcularPrecoDosAdicionais()->centavos)->toBe(0);
        });

        it('calcula o preço dos adicionais corretamente (1 adicional)', function () {
            $saborEscolhido = new SaborEscolhido(
                id: SaborEscolhido::MENOR_ID_PERMITIDO,
                pizza: $this->pizza,
                ingredientesEscolhidos: [
                    ...$this->ingredientesEscolhidosPadrao,
                    IngredienteEscolhidoFactory::adicional(3, IngredienteFactory::bacon(), 899),
                ],
            );

            expect($saborEscolhido->calcularPrecoDosAdicionais()->centavos)->toBe(899);
        });

        it('calcula o preço dos adicionais corretamente (2 adicionais)', function () {
            $saborEscolhido = new SaborEscolhido(
                id: SaborEscolhido::MENOR_ID_PERMITIDO,
                pizza: $this->pizza,
                ingredientesEscolhidos: [
                    ...$this->ingredientesEscolhidosPadrao,
                    IngredienteEscolhidoFactory::adicional(3, IngredienteFactory::bacon(), 899),
                    IngredienteEscolhidoFactory::adicional(4, IngredienteFactory::catupiry(), 799),
                ],
            );

            expect($saborEscolhido->calcularPrecoDosAdicionais()->centavos)->toBe(1698);
        });

        it('calcula o preço dos adicionais corretamente (2 adicionais repetidos)', function () {
            $saborEscolhido = new SaborEscolhido(
                id: SaborEscolhido::MENOR_ID_PERMITIDO,
                pizza: $this->pizza,
                ingredientesEscolhidos: [
                    ...$this->ingredientesEscolhidosPadrao,
                    IngredienteEscolhidoFactory::adicional(3, IngredienteFactory::bacon(), 899),
                    IngredienteEscolhidoFactory::adicional(4, IngredienteFactory::bacon(), 899),
                ],
            );

            expect($saborEscolhido->calcularPrecoDosAdicionais()->centavos)->toBe(1798);
        });

    });

});
