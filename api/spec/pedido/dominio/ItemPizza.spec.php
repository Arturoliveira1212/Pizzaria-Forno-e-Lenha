<?php

describe('ItemPizza', function () {

    describe('validar()', function () {

        beforeAll(function () {
            $this->pizzaPortuguesa = PizzaFactory::portuguesa();

            $this->saborEscolhidoPortuguesa = new SaborEscolhido(
                id: 1,
                pizza: $this->pizzaPortuguesa,
                ingredientesEscolhidos: [
                    IngredienteEscolhidoFactory::padrao(1, IngredienteFactory::molho()),
                    IngredienteEscolhidoFactory::padrao(2, IngredienteFactory::mussarela()),
                    IngredienteEscolhidoFactory::substituto(3, IngredienteFactory::milho()),
                    IngredienteEscolhidoFactory::adicional(4, IngredienteFactory::bacon(), 899),
                ],
            );

            $this->saborEscolhidoQuatroQueijos = new SaborEscolhido(
                id: 2,
                pizza: PizzaFactory::quatroQueijos(),
                ingredientesEscolhidos: [
                    IngredienteEscolhidoFactory::padrao(1, IngredienteFactory::molho()),
                    IngredienteEscolhidoFactory::padrao(2, IngredienteFactory::mussarela()),
                ],
            );

            $this->saborEscolhidoCalabresa = new SaborEscolhido(
                id: 3,
                pizza: PizzaFactory::calabresa(),
                ingredientesEscolhidos: [
                    IngredienteEscolhidoFactory::padrao(1, IngredienteFactory::molho()),
                    IngredienteEscolhidoFactory::padrao(2, IngredienteFactory::mussarela()),
                ],
            );
        });

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
            })->toThrow(new DominioException('O preço praticado não pode ser zero.'));
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

        beforeAll(function () {
            $this->pizzaPortuguesa = PizzaFactory::portuguesa();

            $this->pizzaQuatroQueijos = PizzaFactory::quatroQueijos();

            $this->saborEscolhidoPortuguesa = new SaborEscolhido(
                id: 1,
                pizza: $this->pizzaPortuguesa,
                ingredientesEscolhidos: [
                    IngredienteEscolhidoFactory::padrao(1, IngredienteFactory::molho()),
                    IngredienteEscolhidoFactory::padrao(2, IngredienteFactory::mussarela()),
                    IngredienteEscolhidoFactory::substituto(1, IngredienteFactory::milho()),
                    IngredienteEscolhidoFactory::adicional(1, IngredienteFactory::bacon(), 899),
                ],
            );

            $this->saborEscolhidoQuatroQueijos = new SaborEscolhido(
                id: 2,
                pizza: $this->pizzaQuatroQueijos,
                ingredientesEscolhidos: [
                    IngredienteEscolhidoFactory::padrao(1, IngredienteFactory::molho()),
                    IngredienteEscolhidoFactory::padrao(2, IngredienteFactory::mussarela()),
                    IngredienteEscolhidoFactory::adicional(3, IngredienteFactory::bacon(), 899),
                    IngredienteEscolhidoFactory::adicional(4, IngredienteFactory::calabresa(), 799),
                    IngredienteEscolhidoFactory::adicional(5, IngredienteFactory::calabresa(), 799),
                ],
            );

            $this->saborEscolhidoPortuguesaMeiaPizza = new SaborEscolhido(
                id: 1,
                pizza: $this->pizzaPortuguesa,
                ingredientesEscolhidos: [
                    IngredienteEscolhidoFactory::padrao(1, IngredienteFactory::molho()),
                    IngredienteEscolhidoFactory::padrao(2, IngredienteFactory::mussarela()),
                    IngredienteEscolhidoFactory::substituto(3, IngredienteFactory::milho()),
                    IngredienteEscolhidoFactory::adicional(4, IngredienteFactory::bacon(), 450),
                ],
            );

            $this->saborEscolhidoQuatroQueijosMeiaPizza = new SaborEscolhido(
                id: 2,
                pizza: $this->pizzaQuatroQueijos,
                ingredientesEscolhidos: [
                    IngredienteEscolhidoFactory::padrao(1, IngredienteFactory::molho()),
                    IngredienteEscolhidoFactory::padrao(2, IngredienteFactory::mussarela()),
                    IngredienteEscolhidoFactory::adicional(1, IngredienteFactory::bacon(), 450),
                    IngredienteEscolhidoFactory::adicional(2, IngredienteFactory::calabresa(), 400),
                    IngredienteEscolhidoFactory::adicional(2, IngredienteFactory::calabresa(), 400),
                ],
            );
        });

        it('calcula o preço total corretamente (1 sabor + 1 quantidade)', function () {
            $itemPizza = new ItemPizza(
                id: ItemPedido::MENOR_ID_PERMITIDO,
                quantidade: 1,
                tamanhoPizza: TamanhoPizza::MEDIA,
                saboresEscolhidos: [$this->saborEscolhidoQuatroQueijos],
            );

            expect($itemPizza->calcularPrecoTotal()->centavos)->toBe(6096);
        });

        it('calcula o preço total corretamente (1 sabor + 2 quantidades)', function () {
            $itemPizza = new ItemPizza(
                id: ItemPedido::MENOR_ID_PERMITIDO,
                quantidade: 2,
                tamanhoPizza: TamanhoPizza::MEDIA,
                saboresEscolhidos: [$this->saborEscolhidoQuatroQueijos],
            );

            expect($itemPizza->calcularPrecoTotal()->centavos)->toBe(12192);
        });

        it('calcula o preço total corretamente (2 sabores + 1 quantidade)', function () {
            $itemPizza = new ItemPizza(
                id: ItemPedido::MENOR_ID_PERMITIDO,
                quantidade: 1,
                tamanhoPizza: TamanhoPizza::MEDIA,
                saboresEscolhidos: [$this->saborEscolhidoPortuguesaMeiaPizza, $this->saborEscolhidoQuatroQueijosMeiaPizza],
            );

            expect($itemPizza->calcularPrecoTotal()->centavos)->toBe(5495);
        });

        it('calcula o preço total corretamente (2 sabores + 2 quantidades)', function () {
            $itemPizza = new ItemPizza(
                id: ItemPedido::MENOR_ID_PERMITIDO,
                quantidade: 2,
                tamanhoPizza: TamanhoPizza::MEDIA,
                saboresEscolhidos: [$this->saborEscolhidoPortuguesaMeiaPizza, $this->saborEscolhidoQuatroQueijosMeiaPizza],
            );

            expect($itemPizza->calcularPrecoTotal()->centavos)->toBe(10990);
        });
    });

    describe('obterDescricaoSabores()', function () {

        beforeAll(function () {
            $this->pizzaPortuguesa = PizzaFactory::portuguesa();

            $this->pizzaQuatroQueijos = PizzaFactory::quatroQueijos();

            $this->saborEscolhidoPortuguesa = new SaborEscolhido(
                id: 1,
                pizza: $this->pizzaPortuguesa,
                ingredientesEscolhidos: [
                    IngredienteEscolhidoFactory::padrao(1, IngredienteFactory::molho()),
                    IngredienteEscolhidoFactory::padrao(2, IngredienteFactory::mussarela()),
                    IngredienteEscolhidoFactory::substituto(3, IngredienteFactory::milho()),
                    IngredienteEscolhidoFactory::adicional(4, IngredienteFactory::bacon(), 899),
                ],
            );

            $this->saborEscolhidoQuatroQueijos = new SaborEscolhido(
                id: 2,
                pizza: $this->pizzaQuatroQueijos,
                ingredientesEscolhidos: [
                    IngredienteEscolhidoFactory::padrao(1, IngredienteFactory::molho()),
                    IngredienteEscolhidoFactory::padrao(2, IngredienteFactory::mussarela()),
                ],
            );
        });

        it('retorna a descrição correta para 1 sabor', function () {
            $itemPizza = new ItemPizza(
                id: ItemPedido::MENOR_ID_PERMITIDO,
                quantidade: 1,
                tamanhoPizza: TamanhoPizza::MEDIA,
                saboresEscolhidos: [$this->saborEscolhidoPortuguesa],
            );

            expect($itemPizza->obterDescricaoSabores())->toBe('Pizza Portuguesa (M)');
        });

        it('retorna a descrição correta para 2 sabores', function () {
            $itemPizza = new ItemPizza(
                id: ItemPedido::MENOR_ID_PERMITIDO,
                quantidade: 1,
                tamanhoPizza: TamanhoPizza::MEDIA,
                saboresEscolhidos: [$this->saborEscolhidoPortuguesa, $this->saborEscolhidoQuatroQueijos],
            );

            expect($itemPizza->obterDescricaoSabores())->toBe('Pizza Portuguesa/Quatro Queijos (M)');
        });
    });

});
