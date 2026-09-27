<?php

describe('ItemPizza', function () {

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

        $this->saborEscolhidoPortuguesa = new SaborEscolhido(
            id: 1,
            pizza: new Pizza(
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
                precosPorTamanho: [
                    new PrecoPorTamanho(id: 1, tamanhoPizza: TamanhoPizza::PEQUENA, preco: new Dinheiro(2000)),
                    new PrecoPorTamanho(id: 2, tamanhoPizza: TamanhoPizza::MEDIA, preco: new Dinheiro(3000)),
                ],
            ),
            ingredientesEscolhidos: [
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
                    id: 1,
                    ingrediente: $this->milho,
                    papel: PapelIngrediente::SUBSTITUTO,
                    custoAdicional: Dinheiro::zero(),
                ),
                new IngredienteEscolhido(
                    id: 1,
                    ingrediente: $this->bacon,
                    papel: PapelIngrediente::ADICIONAL,
                    custoAdicional: new Dinheiro(500),
                ),
            ],
        );

        $this->saborEscolhidoQuatroQueijos = new SaborEscolhido(
            id: 2,
            pizza: new Pizza(
                id: 2,
                nome: 'Quatro Queijos',
                descricao: 'Descrição',
                ingredientesPadrao: [
                    new IngredientePadrao(id: 1, ingrediente: $this->molho, removivel: false, substituivel: false),
                    new IngredientePadrao(id: 2, ingrediente: $this->mussarela, removivel: false, substituivel: false),
                    new IngredientePadrao(id: 3, ingrediente: $this->catupiry, removivel: true, substituivel: true),
                    new IngredientePadrao(id: 4, ingrediente: $this->manjericao, removivel: true, substituivel: true),
                ],
                ingredientesSubstitutos: [$this->cebola, $this->milho],
                adicionais: [
                    new IngredienteAdicional(id: 1, ingrediente: $this->bacon, preco: new Dinheiro(500)),
                    new IngredienteAdicional(id: 2, ingrediente: $this->calabresa, preco: new Dinheiro(700)),
                ],
                precosPorTamanho: [
                    new PrecoPorTamanho(id: 1, tamanhoPizza: TamanhoPizza::PEQUENA, preco: new Dinheiro(2500)),
                    new PrecoPorTamanho(id: 2, tamanhoPizza: TamanhoPizza::MEDIA, preco: new Dinheiro(3500)),
                    new PrecoPorTamanho(id: 3, tamanhoPizza: TamanhoPizza::GRANDE, preco: new Dinheiro(4500)),
                ],
            ),
            ingredientesEscolhidos: [
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
                    id: 4,
                    ingrediente: $this->manjericao,
                    papel: PapelIngrediente::PADRAO,
                    custoAdicional: Dinheiro::zero(),
                ),
            ],
        );

        $this->saborEscolhidoCalabresa = new SaborEscolhido(
            id: 3,
            pizza: new Pizza(
                id: 3,
                nome: 'Calabresa',
                descricao: 'Descrição',
                ingredientesPadrao: [
                    new IngredientePadrao(id: 1, ingrediente: $this->molho, removivel: false, substituivel: false),
                    new IngredientePadrao(id: 2, ingrediente: $this->mussarela, removivel: false, substituivel: false),
                    new IngredientePadrao(id: 3, ingrediente: $this->calabresa, removivel: true, substituivel: true),
                ],
                ingredientesSubstitutos: [$this->cebola, $this->milho],
                adicionais: [
                    new IngredienteAdicional(id: 1, ingrediente: $this->bacon, preco: new Dinheiro(500)),
                ],
                precosPorTamanho: [
                    new PrecoPorTamanho(id: 1, tamanhoPizza: TamanhoPizza::PEQUENA, preco: new Dinheiro(2200)),
                    new PrecoPorTamanho(id: 2, tamanhoPizza: TamanhoPizza::MEDIA, preco: new Dinheiro(3200)),
                    new PrecoPorTamanho(id: 3, tamanhoPizza: TamanhoPizza::GRANDE, preco: new Dinheiro(4200)),
                ],
            ),
            ingredientesEscolhidos: [
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
            ],
        );

        it('lança exceção quando o identificador é menor que o mínimo permitido', function () {
            expect(function () {
                $id = ItemPedido::MENOR_ID_PERMITIDO - 1;

                return new ItemPizza(
                    id: $id,
                    quantidade: ItemPedido::QUANTIDADE_MINIMA_PERMITIDA,
                    tamanhoPizza: TamanhoPizza::MEDIA,
                    saboresEscolhidos: [$this->saborEscolhidoPortuguesa],
                );
            })->toThrow(new DominioException('O identificador do ingrediente escolhido deve ser maior que ' . ItemPedido::MENOR_ID_PERMITIDO . '.'));
        });

        it('lança exceção quando a quantidade é menor que o mínimo permitido', function () {
            expect(function () {
                $quantidade = ItemPedido::QUANTIDADE_MINIMA_PERMITIDA - 1;

                return new ItemPizza(
                    id: ItemPedido::MENOR_ID_PERMITIDO,
                    quantidade: $quantidade,
                    tamanhoPizza: TamanhoPizza::MEDIA,
                    saboresEscolhidos: [$this->saborEscolhidoPortuguesa],
                );
            })->toThrow(new DominioException('A quantidade deve ser maior ou igual a ' . ItemPedido::QUANTIDADE_MINIMA_PERMITIDA . '.'));
        });

        it('lança exceção quando a quantidade de sabores escolhidos é maior que o máximo permitido', function () {
            expect(function () {
                return new ItemPizza(
                    id: ItemPedido::MENOR_ID_PERMITIDO,
                    quantidade: ItemPedido::QUANTIDADE_MINIMA_PERMITIDA,
                    tamanhoPizza: TamanhoPizza::MEDIA,
                    saboresEscolhidos: [$this->saborEscolhidoPortuguesa, $this->saborEscolhidoQuatroQueijos, $this->saborEscolhidoCalabresa],
                );
            })->toThrow(new DominioException('A quantidade de sabores deve ser entre ' . ItemPizza::MINIMO_SABORES_PERMITIDOS . ' e ' . ItemPizza::MAXIMO_SABORES_PERMITIDOS . '.'));
        });

        it('lança exceção quando a quantidade de sabores escolhidos é menor que o mínimo permitido', function () {
            expect(function () {
                return new ItemPizza(
                    id: ItemPedido::MENOR_ID_PERMITIDO,
                    quantidade: ItemPedido::QUANTIDADE_MINIMA_PERMITIDA,
                    tamanhoPizza: TamanhoPizza::MEDIA,
                    saboresEscolhidos: [],
                    precoPraticado: new Dinheiro(3000),
                );
            })->toThrow(new DominioException('A quantidade de sabores deve ser entre ' . ItemPizza::MINIMO_SABORES_PERMITIDOS . ' e ' . ItemPizza::MAXIMO_SABORES_PERMITIDOS . '.'));
        });

        it('lança exceção quando um sabor escolhido é selecionado mais de uma vez', function () {
            expect(function () {
                return new ItemPizza(
                    id: ItemPedido::MENOR_ID_PERMITIDO,
                    quantidade: ItemPedido::QUANTIDADE_MINIMA_PERMITIDA,
                    tamanhoPizza: TamanhoPizza::MEDIA,
                    saboresEscolhidos: [$this->saborEscolhidoPortuguesa, $this->saborEscolhidoPortuguesa],
                );
            })->toThrow(new DominioException('O sabor Portuguesa foi escolhido mais de uma vez.'));
        });

        it('lança exceção quando um sabor escolhido não está disponível para o tamanho selecionado', function () {
            expect(function () {
                return new ItemPizza(
                    id: ItemPedido::MENOR_ID_PERMITIDO,
                    quantidade: ItemPedido::QUANTIDADE_MINIMA_PERMITIDA,
                    tamanhoPizza: TamanhoPizza::GRANDE,
                    saboresEscolhidos: [$this->saborEscolhidoPortuguesa, $this->saborEscolhidoQuatroQueijos],
                );
            })->toThrow(new DominioException('O sabor Portuguesa não está disponível para o tamanho selecionado.'));
        });

        it('lança exceção quando o preço praticado é menor ou igual a zero', function () {
            expect(function () {
                return new ItemPizza(
                    id: ItemPedido::MENOR_ID_PERMITIDO,
                    quantidade: ItemPedido::QUANTIDADE_MINIMA_PERMITIDA,
                    tamanhoPizza: TamanhoPizza::MEDIA,
                    saboresEscolhidos: [$this->saborEscolhidoPortuguesa],
                    precoPraticado: Dinheiro::zero(),
                );
            })->toThrow(new DominioException('O preço praticado não pode ser menor ou igual a zero.'));
        });

        it('cria item pizza quando todos os campos são válidos', function () {
            $itemPizza = new ItemPizza(
                id: ItemPedido::MENOR_ID_PERMITIDO,
                quantidade: ItemPedido::QUANTIDADE_MINIMA_PERMITIDA,
                tamanhoPizza: TamanhoPizza::MEDIA,
                saboresEscolhidos: [$this->saborEscolhidoPortuguesa, $this->saborEscolhidoQuatroQueijos],
            );

            expect($itemPizza->id)->toBe(ItemPedido::MENOR_ID_PERMITIDO);
            expect($itemPizza->quantidade)->toBe(1);
            expect($itemPizza->tamanhoPizza)->toBe(TamanhoPizza::MEDIA);
            expect($itemPizza->saboresEscolhidos)->toEqual([$this->saborEscolhidoPortuguesa, $this->saborEscolhidoQuatroQueijos]);
        });
    });

    describe('calcularPrecoTotal()', function () {

        $this->saborEscolhidoPortuguesa = new SaborEscolhido(
            id: 1,
            pizza: new Pizza(
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
                    new IngredienteAdicional(id: 1, ingrediente: $this->bacon, preco: new Dinheiro(899)),
                ],
                precosPorTamanho: [
                    new PrecoPorTamanho(id: 1, tamanhoPizza: TamanhoPizza::PEQUENA, preco: new Dinheiro(2990)),
                    new PrecoPorTamanho(id: 2, tamanhoPizza: TamanhoPizza::MEDIA, preco: new Dinheiro(3990)),
                ],
            ),
            ingredientesEscolhidos: [
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
                    id: 1,
                    ingrediente: $this->milho,
                    papel: PapelIngrediente::SUBSTITUTO,
                    custoAdicional: Dinheiro::zero(),
                ),
                new IngredienteEscolhido(
                    id: 1,
                    ingrediente: $this->bacon,
                    papel: PapelIngrediente::ADICIONAL,
                    custoAdicional: new Dinheiro(899),
                ),
            ],
        );

        $this->saborEscolhidoQuatroQueijos = new SaborEscolhido(
            id: 2,
            pizza: new Pizza(
                id: 2,
                nome: 'Quatro Queijos',
                descricao: 'Descrição',
                ingredientesPadrao: [
                    new IngredientePadrao(id: 1, ingrediente: $this->molho, removivel: false, substituivel: false),
                    new IngredientePadrao(id: 2, ingrediente: $this->mussarela, removivel: false, substituivel: false),
                    new IngredientePadrao(id: 3, ingrediente: $this->catupiry, removivel: true, substituivel: true),
                    new IngredientePadrao(id: 4, ingrediente: $this->manjericao, removivel: true, substituivel: true),
                ],
                ingredientesSubstitutos: [$this->cebola, $this->milho],
                adicionais: [
                    new IngredienteAdicional(id: 1, ingrediente: $this->bacon, preco: new Dinheiro(899)),
                    new IngredienteAdicional(id: 2, ingrediente: $this->calabresa, preco: new Dinheiro(1099)),
                ],
                precosPorTamanho: [
                    new PrecoPorTamanho(id: 1, tamanhoPizza: TamanhoPizza::PEQUENA, preco: new Dinheiro(2599)),
                    new PrecoPorTamanho(id: 2, tamanhoPizza: TamanhoPizza::MEDIA, preco: new Dinheiro(3599)),
                    new PrecoPorTamanho(id: 3, tamanhoPizza: TamanhoPizza::GRANDE, preco: new Dinheiro(4590)),
                ],
            ),
            ingredientesEscolhidos: [
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
                    id: 4,
                    ingrediente: $this->manjericao,
                    papel: PapelIngrediente::PADRAO,
                    custoAdicional: Dinheiro::zero(),
                ),
                new IngredienteEscolhido(
                    id: 1,
                    ingrediente: $this->bacon,
                    papel: PapelIngrediente::ADICIONAL,
                    custoAdicional: new Dinheiro(899),
                ),
                new IngredienteEscolhido(
                    id: 2,
                    ingrediente: $this->calabresa,
                    papel: PapelIngrediente::ADICIONAL,
                    custoAdicional: new Dinheiro(1099),
                ),
                new IngredienteEscolhido(
                    id: 2,
                    ingrediente: $this->calabresa,
                    papel: PapelIngrediente::ADICIONAL,
                    custoAdicional: new Dinheiro(1099),
                ),
            ],
        );

        it('calcula o preço total corretamente (1 sabor + 1 quantidade)', function () {
            $itemPizza = new ItemPizza(
                id: ItemPedido::MENOR_ID_PERMITIDO,
                quantidade: 1,
                tamanhoPizza: TamanhoPizza::MEDIA,
                saboresEscolhidos: [$this->saborEscolhidoQuatroQueijos],
            );

            expect($itemPizza->calcularPrecoTotal()->centavos)->toBe(6696);
        });

        it('calcula o preço total corretamente (1 sabor + 2 quantidades)', function () {
            $itemPizza = new ItemPizza(
                id: ItemPedido::MENOR_ID_PERMITIDO,
                quantidade: 2,
                tamanhoPizza: TamanhoPizza::MEDIA,
                saboresEscolhidos: [$this->saborEscolhidoQuatroQueijos],
            );

            expect($itemPizza->calcularPrecoTotal()->centavos)->toBe(13392);
        });

        it('calcula o preço total corretamente (2 sabores + 1 quantidade)', function () {
            $itemPizza = new ItemPizza(
                id: ItemPedido::MENOR_ID_PERMITIDO,
                quantidade: 1,
                tamanhoPizza: TamanhoPizza::MEDIA,
                saboresEscolhidos: [$this->saborEscolhidoPortuguesa, $this->saborEscolhidoQuatroQueijos],
            );

            expect($itemPizza->calcularPrecoTotal()->centavos)->toBe(5793);
        });

        it('calcula o preço total corretamente (2 sabores + 2 quantidades)', function () {
            $itemPizza = new ItemPizza(
                id: ItemPedido::MENOR_ID_PERMITIDO,
                quantidade: 2,
                tamanhoPizza: TamanhoPizza::MEDIA,
                saboresEscolhidos: [$this->saborEscolhidoPortuguesa, $this->saborEscolhidoQuatroQueijos],
            );

            expect($itemPizza->calcularPrecoTotal()->centavos)->toBe(11586);
        });
    });

});
