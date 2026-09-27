<?php

describe('SaborEscolhido', function () {

    $this->molho = new Ingrediente(1, 'Molho');
    $this->mussarela = new Ingrediente(2, 'Mussarela');
    $this->presunto = new Ingrediente(3, 'Presunto');
    $this->azeitona = new Ingrediente(4, 'Azeitona');
    $this->cebola = new Ingrediente(5, 'Cebola');
    $this->milho = new Ingrediente(6, 'Milho');
    $this->bacon = new Ingrediente(7, 'Bacon');
    $this->catupiry = new Ingrediente(8, 'Catupiry');
    $this->manjericao = new Ingrediente(9, 'Manjericão');
    $this->calabresa = new Ingrediente(10, 'Calabresa');

    describe('validar()', function () {

        $this->pizza = new Pizza(
            id: 1,
            nome: 'Portuguesa',
            descricao: 'Descrição',
            ingredientesPadrao: [
                new IngredientePadrao(id: 1, ingrediente: $this->molho, removivel: false, substituivel: false),
                new IngredientePadrao(id: 2, ingrediente: $this->mussarela, removivel: false, substituivel: false),
                new IngredientePadrao(id: 3, ingrediente: $this->presunto, removivel: true, substituivel: true),
                new IngredientePadrao(id: 4, ingrediente: $this->azeitona, removivel: true, substituivel: true),
            ],
            ingredientesSubstitutos: [$this->cebola, $this->milho],
            adicionais: [
                new IngredienteAdicional(id: 1, ingrediente: $this->bacon, preco: new Dinheiro(500)),
            ],
            precosPorTamanho: [],
        );

        $this->ingredientesEscolhidosNaoPersonalizaveis = [
            new IngredienteEscolhido(
                id: 1,
                ingrediente: $this->molho,
                papel: PapelIngrediente::PADRAO,
                custoAdicional: Dinheiro::zero(),
            ),
            new IngredienteEscolhido(
                id: 2,
                ingrediente: $this->mussarela,
                papel: PapelIngrediente::PADRAO,
                custoAdicional: Dinheiro::zero(),
            ),
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
                        new IngredienteEscolhido(
                            id: 1,
                            ingrediente: $this->molho,
                            papel: PapelIngrediente::PADRAO,
                            custoAdicional: Dinheiro::zero(),
                        ),
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
                        new IngredienteEscolhido(
                            id: 1,
                            ingrediente: $this->catupiry,
                            papel: PapelIngrediente::PADRAO,
                            custoAdicional: Dinheiro::zero(),
                        ),
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
                        new IngredienteEscolhido(
                            id: 1,
                            ingrediente: $this->bacon,
                            papel: PapelIngrediente::SUBSTITUTO,
                            custoAdicional: Dinheiro::zero(),
                        ),
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
                        new IngredienteEscolhido(
                            id: 1,
                            ingrediente: $this->cebola,
                            papel: PapelIngrediente::ADICIONAL,
                            custoAdicional: new Dinheiro(500),
                        ),
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
                        new IngredienteEscolhido(
                            id: 1,
                            ingrediente: $this->bacon,
                            papel: PapelIngrediente::ADICIONAL,
                            custoAdicional: new Dinheiro(400),
                        ),
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
                        new IngredienteEscolhido(
                            id: 1,
                            ingrediente: $this->molho,
                            papel: PapelIngrediente::PADRAO,
                            custoAdicional: Dinheiro::zero(),
                        ),
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
                        new IngredienteEscolhido(
                            id: 1,
                            ingrediente: $this->cebola,
                            papel: PapelIngrediente::SUBSTITUTO,
                            custoAdicional: Dinheiro::zero(),
                        ),
                        new IngredienteEscolhido(
                            id: 2,
                            ingrediente: $this->milho,
                            papel: PapelIngrediente::SUBSTITUTO,
                            custoAdicional: Dinheiro::zero(),
                        ),
                        new IngredienteEscolhido(
                            id: 3,
                            ingrediente: $this->milho,
                            papel: PapelIngrediente::SUBSTITUTO,
                            custoAdicional: Dinheiro::zero(),
                        ),
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
                        new IngredienteEscolhido(
                            id: 1,
                            ingrediente: $this->presunto,
                            papel: PapelIngrediente::PADRAO,
                            custoAdicional: Dinheiro::zero(),
                        ),
                        new IngredienteEscolhido(
                            id: 2,
                            ingrediente: $this->milho,
                            papel: PapelIngrediente::SUBSTITUTO,
                            custoAdicional: Dinheiro::zero(),
                        ),
                        new IngredienteEscolhido(
                            id: 3,
                            ingrediente: $this->cebola,
                            papel: PapelIngrediente::SUBSTITUTO,
                            custoAdicional: Dinheiro::zero(),
                        ),
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
                    new IngredienteEscolhido(
                        id: 1,
                        ingrediente: $this->presunto,
                        papel: PapelIngrediente::PADRAO,
                        custoAdicional: Dinheiro::zero(),
                    ),
                    new IngredienteEscolhido(
                        id: 2,
                        ingrediente: $this->azeitona,
                        papel: PapelIngrediente::PADRAO,
                        custoAdicional: Dinheiro::zero(),
                    ),
                    new IngredienteEscolhido(
                        id: 3,
                        ingrediente: $this->bacon,
                        papel: PapelIngrediente::ADICIONAL,
                        custoAdicional: new Dinheiro(500),
                    ),
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
                    new IngredienteEscolhido(
                        id: 1,
                        ingrediente: $this->presunto,
                        papel: PapelIngrediente::PADRAO,
                        custoAdicional: Dinheiro::zero(),
                    ),
                    new IngredienteEscolhido(
                        id: 2,
                        ingrediente: $this->cebola,
                        papel: PapelIngrediente::SUBSTITUTO,
                        custoAdicional: Dinheiro::zero(),
                    ),
                    new IngredienteEscolhido(
                        id: 3,
                        ingrediente: $this->bacon,
                        papel: PapelIngrediente::ADICIONAL,
                        custoAdicional: new Dinheiro(500),
                    ),
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
                    new IngredienteEscolhido(
                        id: 1,
                        ingrediente: $this->milho,
                        papel: PapelIngrediente::SUBSTITUTO,
                        custoAdicional: Dinheiro::zero(),
                    ),
                    new IngredienteEscolhido(
                        id: 2,
                        ingrediente: $this->cebola,
                        papel: PapelIngrediente::SUBSTITUTO,
                        custoAdicional: Dinheiro::zero(),
                    ),
                    new IngredienteEscolhido(
                        id: 3,
                        ingrediente: $this->bacon,
                        papel: PapelIngrediente::ADICIONAL,
                        custoAdicional: new Dinheiro(500),
                    ),
                ],
            );

            expect($saborEscolhido->id)->toBe($id);
            expect($saborEscolhido->pizza)->toBe($this->pizza);
            expect($saborEscolhido->ingredientesEscolhidos)->toHaveLength(5);
        });

    });

    describe('calcularPrecoDosAdicionais()', function () {

        $this->pizza = new Pizza(
            id: 1,
            nome: 'Marguerita',
            descricao: 'Descrição',
            ingredientesPadrao: [
                new IngredientePadrao(id: 1, ingrediente: $this->molho, removivel: false, substituivel: false),
                new IngredientePadrao(id: 2, ingrediente: $this->mussarela, removivel: false, substituivel: false),
                new IngredientePadrao(id: 3, ingrediente: $this->manjericao, removivel: true, substituivel: true),
            ],
            ingredientesSubstitutos: [$this->cebola, $this->milho],
            adicionais: [
                new IngredienteAdicional(id: 1, ingrediente: $this->bacon, preco: new Dinheiro(599)),
                new IngredienteAdicional(id: 2, ingrediente: $this->catupiry, preco: new Dinheiro(799)),
                new IngredienteAdicional(id: 3, ingrediente: $this->calabresa, preco: new Dinheiro(699)),
            ],
            precosPorTamanho: [],
        );

        $this->ingredientesEscolhidosPadrao = [
            new IngredienteEscolhido(
                id: 1,
                ingrediente: $this->molho,
                papel: PapelIngrediente::PADRAO,
                custoAdicional: Dinheiro::zero(),
            ),
            new IngredienteEscolhido(
                id: 2,
                ingrediente: $this->mussarela,
                papel: PapelIngrediente::PADRAO,
                custoAdicional: Dinheiro::zero(),
            ),
            new IngredienteEscolhido(
                id: 3,
                ingrediente: $this->manjericao,
                papel: PapelIngrediente::PADRAO,
                custoAdicional: Dinheiro::zero(),
            ),
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
                    new IngredienteEscolhido(
                        id: 3,
                        ingrediente: $this->bacon,
                        papel: PapelIngrediente::ADICIONAL,
                        custoAdicional: new Dinheiro(599),
                    ),
                ],
            );

            expect($saborEscolhido->calcularPrecoDosAdicionais()->centavos)->toBe(599);
        });

        it('calcula o preço dos adicionais corretamente (2 adicionais)', function () {
            $saborEscolhido = new SaborEscolhido(
                id: SaborEscolhido::MENOR_ID_PERMITIDO,
                pizza: $this->pizza,
                ingredientesEscolhidos: [
                    ...$this->ingredientesEscolhidosPadrao,
                    new IngredienteEscolhido(
                        id: 3,
                        ingrediente: $this->bacon,
                        papel: PapelIngrediente::ADICIONAL,
                        custoAdicional: new Dinheiro(599),
                    ),
                    new IngredienteEscolhido(
                        id: 4,
                        ingrediente: $this->catupiry,
                        papel: PapelIngrediente::ADICIONAL,
                        custoAdicional: new Dinheiro(799),
                    ),
                ],
            );

            expect($saborEscolhido->calcularPrecoDosAdicionais()->centavos)->toBe(1398);
        });

        it('calcula o preço dos adicionais corretamente (2 adicionais repetidos)', function () {
            $saborEscolhido = new SaborEscolhido(
                id: SaborEscolhido::MENOR_ID_PERMITIDO,
                pizza: $this->pizza,
                ingredientesEscolhidos: [
                    ...$this->ingredientesEscolhidosPadrao,
                    new IngredienteEscolhido(
                        id: 3,
                        ingrediente: $this->bacon,
                        papel: PapelIngrediente::ADICIONAL,
                        custoAdicional: new Dinheiro(599),
                    ),
                    new IngredienteEscolhido(
                        id: 4,
                        ingrediente: $this->bacon,
                        papel: PapelIngrediente::ADICIONAL,
                        custoAdicional: new Dinheiro(599),
                    ),
                    new IngredienteEscolhido(
                        id: 5,
                        ingrediente: $this->calabresa,
                        papel: PapelIngrediente::ADICIONAL,
                        custoAdicional: new Dinheiro(699),
                    ),
                ],
            );

            expect($saborEscolhido->calcularPrecoDosAdicionais()->centavos)->toBe(1897);
        });

    });

});
