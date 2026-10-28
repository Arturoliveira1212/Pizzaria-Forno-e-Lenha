<?php

describe('ValidadorEstoqueItemPedido', function () {

    describe('validarEstoqueItensSimples()', function () {

        it('preenche erros corretamente quando o produto simples está sem estoque', function () {
            $erros = [];

            $validador = new ValidadorEstoqueItemPedido();
            $validador->validarEstoqueItensPedido(
                itensPedido: [
                    new ItemSimples(
                        id: 1,
                        quantidade: 1,
                        produtoSimples: ProdutoSimplesFactory::cocaCola(0),
                    ),
                ],
                erros: $erros,
            );

            expect($erros)->toBe(['Produto Coca-Cola (ID 1) está sem estoque. Retire o produto do pedido.']);
        });

        it('preenche erros corretamente quando a quantidade do produto simples é maior que o estoque disponível (1 item simples)', function () {
            $erros = [];

            $validador = new ValidadorEstoqueItemPedido();
            $validador->validarEstoqueItensPedido(
                itensPedido: [
                    new ItemSimples(
                        id: 1,
                        quantidade: 3,
                        produtoSimples: ProdutoSimplesFactory::cocaCola(2),
                    ),
                ],
                erros: $erros,
            );

            expect($erros)->toBe(['Produto Coca-Cola (ID 1) não possui estoque suficiente. Quantidade em estoque: 2.']);
        });

        it('preenche erros corretamente quando a quantidade de um produto simples é maior que o estoque disponível (2 itens simples)', function () {
            $erros = [];

            $validador = new ValidadorEstoqueItemPedido();
            $validador->validarEstoqueItensPedido(
                itensPedido: [
                    new ItemSimples(
                        id: 1,
                        quantidade: 3,
                        produtoSimples: ProdutoSimplesFactory::cocaCola(2),
                    ),
                    new ItemSimples(
                        id: 2,
                        quantidade: 4,
                        produtoSimples: ProdutoSimplesFactory::sucoLaranja(12),
                    ),
                ],
                erros: $erros,
            );

            expect($erros)->toBe(['Produto Coca-Cola (ID 1) não possui estoque suficiente. Quantidade em estoque: 2.']);
        });

        it('preenche erros corretamente quando todos os itens simples estão sem estoque', function () {
            $erros = [];

            $validador = new ValidadorEstoqueItemPedido();
            $validador->validarEstoqueItensPedido(
                itensPedido: [
                    new ItemSimples(
                        id: 1,
                        quantidade: 3,
                        produtoSimples: ProdutoSimplesFactory::cocaCola(0),
                    ),
                    new ItemSimples(
                        id: 2,
                        quantidade: 4,
                        produtoSimples: ProdutoSimplesFactory::sucoLaranja(0),
                    ),
                ],
                erros: $erros,
            );

            expect($erros)->toBe([
                'Produto Coca-Cola (ID 1) está sem estoque. Retire o produto do pedido.',
                'Produto Suco de Laranja (ID 3) está sem estoque. Retire o produto do pedido.',
            ]);
        });

        it('array de erros vem vazio quando todos os itens simples possuem estoque suficiente', function () {
            $erros = [];

            $validador = new ValidadorEstoqueItemPedido();
            $validador->validarEstoqueItensPedido(
                itensPedido: [
                    new ItemSimples(
                        id: 1,
                        quantidade: 3,
                        produtoSimples: ProdutoSimplesFactory::cocaCola(5),
                    ),
                    new ItemSimples(
                        id: 2,
                        quantidade: 4,
                        produtoSimples: ProdutoSimplesFactory::sucoLaranja(12),
                    ),
                ],
                erros: $erros,
            );

            expect($erros)->toBe([]);
        });

    });

    describe('validarEstoqueItensPizza()', function () {

        beforeAll(function () {
            $this->pizzaCalabresa = PizzaFactory::calabresa();
            $this->pizzaMargherita = PizzaFactory::margherita();
        });

        it('preenche erros corretamente quando consumo de todos os ingrediente é maior que o estoque disponível', function () {
            $erros = [];

            // Possui estoque insuficiente
            $molho = IngredienteFactory::molho(3);
            $mussarela = IngredienteFactory::mussarela(199);
            $bacon = IngredienteFactory::bacon(349);

            $validador = new ValidadorEstoqueItemPedido();
            $validador->validarEstoqueItensPedido(
                itensPedido: [
                    new ItemPizza(
                        id: ItemPedido::MENOR_ID_PERMITIDO,
                        quantidade: ItemPedido::QUANTIDADE_MINIMA_PERMITIDA,
                        tamanhoPizza: TamanhoPizza::MEDIA,
                        saboresEscolhidos: [
                            new SaborEscolhido(
                                id: 3,
                                pizza: $this->pizzaCalabresa,
                                ingredientesEscolhidos: [
                                    IngredienteEscolhidoFactory::padrao(1, $molho),
                                    IngredienteEscolhidoFactory::padrao(2, $mussarela),
                                    IngredienteEscolhidoFactory::adicional(4, $bacon, 899),
                                ],
                            ),
                        ],
                    ),
                ],
                erros: $erros,
            );

            expect($erros)->toBe(['Pizza Calabresa (M) não possui estoque suficiente. Retire ou atualize a quantidade da pizza no pedido.']);
        });

        it('preenche erros corretamente quando consumo de todos os ingrediente é maior que o estoque disponível (2 itens pizza)', function () {
            $erros = [];

            // Possui estoque insuficiente
            $molho = IngredienteFactory::molho(3);
            $mussarela = IngredienteFactory::mussarela(199);
            $bacon = IngredienteFactory::bacon(349);
            $catupiry = IngredienteFactory::catupiry(199);

            $validador = new ValidadorEstoqueItemPedido();
            $validador->validarEstoqueItensPedido(
                itensPedido: [
                    new ItemPizza(
                        id: ItemPedido::MENOR_ID_PERMITIDO,
                        quantidade: ItemPedido::QUANTIDADE_MINIMA_PERMITIDA,
                        tamanhoPizza: TamanhoPizza::MEDIA,
                        saboresEscolhidos: [
                            new SaborEscolhido(
                                id: 3,
                                pizza: $this->pizzaCalabresa,
                                ingredientesEscolhidos: [
                                    IngredienteEscolhidoFactory::padrao(1, $molho),
                                    IngredienteEscolhidoFactory::padrao(2, $mussarela),
                                    IngredienteEscolhidoFactory::adicional(4, $bacon, 899),
                                ],
                            ),
                        ],
                    ),
                    new ItemPizza(
                        id: ItemPedido::MENOR_ID_PERMITIDO,
                        quantidade: ItemPedido::QUANTIDADE_MINIMA_PERMITIDA,
                        tamanhoPizza: TamanhoPizza::MEDIA,
                        saboresEscolhidos: [
                            new SaborEscolhido(
                                id: 2,
                                pizza: $this->pizzaMargherita,
                                ingredientesEscolhidos: [
                                    IngredienteEscolhidoFactory::padrao(1, $molho),
                                    IngredienteEscolhidoFactory::padrao(2, $mussarela),
                                    IngredienteEscolhidoFactory::padrao(4, $catupiry),
                                ],
                            ),
                        ],
                    ),
                ],
                erros: $erros,
            );

            expect($erros)->toBe([
                'Pizza Calabresa (M) não possui estoque suficiente. Retire ou atualize a quantidade da pizza no pedido.',
                'Pizza Margherita (M) não possui estoque suficiente. Retire ou atualize a quantidade da pizza no pedido.',
            ]);
        });

        it('preenche erros corretamente quando consumo de alguns ingredientes é maior que o estoque disponível (2 itens pizza)', function () {
            $erros = [];

            // Possui estoque insuficiente
            $molho = IngredienteFactory::molho(9);
            $mussarela = IngredienteFactory::mussarela(200);
            $catupiry = IngredienteFactory::catupiry(199);
            // Possui estoque suficiente
            $manjericao = IngredienteFactory::manjericao(6);

            $validador = new ValidadorEstoqueItemPedido();
            $validador->validarEstoqueItensPedido(
                itensPedido: [
                    new ItemPizza(
                        id: 1,
                        quantidade: ItemPedido::QUANTIDADE_MINIMA_PERMITIDA,
                        tamanhoPizza: TamanhoPizza::MEDIA,
                        saboresEscolhidos: [
                            new SaborEscolhido(
                                id: 3,
                                pizza: $this->pizzaCalabresa,
                                ingredientesEscolhidos: [
                                    IngredienteEscolhidoFactory::padrao(1, $molho),
                                    IngredienteEscolhidoFactory::padrao(2, $mussarela),
                                ],
                            ),
                        ],
                    ),
                    new ItemPizza(
                        id: 2,
                        quantidade: ItemPedido::QUANTIDADE_MINIMA_PERMITIDA,
                        tamanhoPizza: TamanhoPizza::GRANDE,
                        saboresEscolhidos: [
                            new SaborEscolhido(
                                id: 2,
                                pizza: $this->pizzaMargherita,
                                ingredientesEscolhidos: [
                                    IngredienteEscolhidoFactory::padrao(1, $molho),
                                    IngredienteEscolhidoFactory::padrao(2, $mussarela),
                                    IngredienteEscolhidoFactory::padrao(2, $manjericao),
                                    IngredienteEscolhidoFactory::padrao(3, $catupiry),
                                ],
                            ),
                        ],
                    ),
                ],
                erros: $erros,
            );

            expect($erros)->toBe(['Pizza Margherita (G) não possui estoque suficiente. Retire ou atualize a quantidade da pizza no pedido.']);
        });

        it('array de erros vem vazio quando todos os ingredientes possuem estoque suficiente', function () {
            $erros = [];

            // Possui estoque suficiente
            $molho = IngredienteFactory::molho(10);
            $mussarela = IngredienteFactory::mussarela(500);
            $catupiry = IngredienteFactory::catupiry(300);
            $manjericao = IngredienteFactory::manjericao(10);

            $validador = new ValidadorEstoqueItemPedido();
            $validador->validarEstoqueItensPedido(
                itensPedido: [
                    new ItemPizza(
                        id: 1,
                        quantidade: ItemPedido::QUANTIDADE_MINIMA_PERMITIDA,
                        tamanhoPizza: TamanhoPizza::MEDIA,
                        saboresEscolhidos: [
                            new SaborEscolhido(
                                id: 3,
                                pizza: $this->pizzaCalabresa,
                                ingredientesEscolhidos: [
                                    IngredienteEscolhidoFactory::padrao(1, $molho),
                                    IngredienteEscolhidoFactory::padrao(2, $mussarela),
                                ],
                            ),
                        ],
                    ),
                    new ItemPizza(
                        id: 2,
                        quantidade: ItemPedido::QUANTIDADE_MINIMA_PERMITIDA,
                        tamanhoPizza: TamanhoPizza::GRANDE,
                        saboresEscolhidos: [
                            new SaborEscolhido(
                                id: 2,
                                pizza: $this->pizzaMargherita,
                                ingredientesEscolhidos: [
                                    IngredienteEscolhidoFactory::padrao(1, $molho),
                                    IngredienteEscolhidoFactory::padrao(2, $mussarela),
                                    IngredienteEscolhidoFactory::padrao(2, $manjericao),
                                    IngredienteEscolhidoFactory::padrao(3, $catupiry),
                                ],
                            ),
                        ],
                    ),
                ],
                erros: $erros,
            );

            expect($erros)->toBe([]);
        });

        it('preenche erros corretamente quando o consumo de alguns ingredientes é maior que o estoque disponível (meia pizza)', function () {
            $erros = [];

            // Possui estoque insuficiente
            $molho = IngredienteFactory::molho(2);
            $mussarela = IngredienteFactory::mussarela(199);
            // Possui estoque suficiente
            $catupiry = IngredienteFactory::catupiry(300);
            $manjericao = IngredienteFactory::manjericao(3);

            $validador = new ValidadorEstoqueItemPedido();
            $validador->validarEstoqueItensPedido(
                itensPedido: [
                    new ItemPizza(
                        id: 1,
                        quantidade: ItemPedido::QUANTIDADE_MINIMA_PERMITIDA,
                        tamanhoPizza: TamanhoPizza::MEDIA,
                        saboresEscolhidos: [
                            new SaborEscolhido(
                                id: 3,
                                pizza: $this->pizzaCalabresa,
                                ingredientesEscolhidos: [
                                    IngredienteEscolhidoFactory::padrao(1, $molho),
                                    IngredienteEscolhidoFactory::padrao(2, $mussarela),
                                ],
                            ),
                            new SaborEscolhido(
                                id: 2,
                                pizza: $this->pizzaMargherita,
                                ingredientesEscolhidos: [
                                    IngredienteEscolhidoFactory::padrao(1, $molho),
                                    IngredienteEscolhidoFactory::padrao(2, $mussarela),
                                    IngredienteEscolhidoFactory::padrao(2, $manjericao),
                                    IngredienteEscolhidoFactory::padrao(3, $catupiry),
                                ],
                            ),
                        ],
                    ),
                ],
                erros: $erros,
            );

            expect($erros)->toBe(['Pizza Calabresa/Margherita (M) não possui estoque suficiente. Retire ou atualize a quantidade da pizza no pedido.']);
        });

        it('preenche erros corretamente quando o consumo de alguns ingredientes é maior que o estoque disponível (meia pizza + quantidade 2)', function () {
            $erros = [];

            // Possui estoque insuficiente
            $molho = IngredienteFactory::molho(6);
            $mussarela = IngredienteFactory::mussarela(300);
            $catupiry = IngredienteFactory::catupiry(150);
            $manjericao = IngredienteFactory::manjericao(3);

            $validador = new ValidadorEstoqueItemPedido();
            $validador->validarEstoqueItensPedido(
                itensPedido: [
                    new ItemPizza(
                        id: 1,
                        quantidade: 2,
                        tamanhoPizza: TamanhoPizza::GRANDE,
                        saboresEscolhidos: [
                            new SaborEscolhido(
                                id: 3,
                                pizza: $this->pizzaCalabresa,
                                ingredientesEscolhidos: [
                                    IngredienteEscolhidoFactory::padrao(1, $molho),
                                    IngredienteEscolhidoFactory::padrao(2, $mussarela),
                                ],
                            ),
                            new SaborEscolhido(
                                id: 2,
                                pizza: $this->pizzaMargherita,
                                ingredientesEscolhidos: [
                                    IngredienteEscolhidoFactory::padrao(1, $molho),
                                    IngredienteEscolhidoFactory::padrao(2, $mussarela),
                                    IngredienteEscolhidoFactory::padrao(2, $manjericao),
                                    IngredienteEscolhidoFactory::padrao(3, $catupiry),
                                ],
                            ),
                        ],
                    ),
                ],
                erros: $erros,
            );

            expect($erros)->toBe(['Pizza Calabresa/Margherita (G) não possui estoque suficiente. Retire ou atualize a quantidade da pizza no pedido.']);
        });

        it('array de erros vem vazio quando todos os ingredientes possuem estoque suficiente (meia pizza)', function () {
            $erros = [];

            // Possui estoque suficiente
            $molho = IngredienteFactory::molho(6);
            $mussarela = IngredienteFactory::mussarela(300);
            $catupiry = IngredienteFactory::catupiry(150);
            $manjericao = IngredienteFactory::manjericao(3);

            $validador = new ValidadorEstoqueItemPedido();
            $validador->validarEstoqueItensPedido(
                itensPedido: [
                    new ItemPizza(
                        id: 1,
                        quantidade: ItemPedido::QUANTIDADE_MINIMA_PERMITIDA,
                        tamanhoPizza: TamanhoPizza::GRANDE,
                        saboresEscolhidos: [
                            new SaborEscolhido(
                                id: 3,
                                pizza: $this->pizzaCalabresa,
                                ingredientesEscolhidos: [
                                    IngredienteEscolhidoFactory::padrao(1, $molho),
                                    IngredienteEscolhidoFactory::padrao(2, $mussarela),
                                ],
                            ),
                            new SaborEscolhido(
                                id: 2,
                                pizza: $this->pizzaMargherita,
                                ingredientesEscolhidos: [
                                    IngredienteEscolhidoFactory::padrao(1, $molho),
                                    IngredienteEscolhidoFactory::padrao(2, $mussarela),
                                    IngredienteEscolhidoFactory::padrao(2, $manjericao),
                                    IngredienteEscolhidoFactory::padrao(3, $catupiry),
                                ],
                            ),
                        ],
                    ),
                ],
                erros: $erros,
            );

            expect($erros)->toBe([]);
        });

    });
});
