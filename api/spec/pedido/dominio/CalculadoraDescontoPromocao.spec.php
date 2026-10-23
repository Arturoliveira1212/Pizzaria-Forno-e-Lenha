<?php

describe('CalculadoraDescontoPromocao', function () {

    beforeAll(function () {
        $this->pizzaCalabresa = PizzaFactory::calabresa();

        $this->pizzaMargherita = PizzaFactory::margherita();

        $this->saborEscolhidoCalabresa = new SaborEscolhido(
            id: 3,
            pizza: $this->pizzaCalabresa,
            ingredientesEscolhidos: [
                IngredienteEscolhidoFactory::padrao(1, IngredienteFactory::molho()),
                IngredienteEscolhidoFactory::padrao(2, IngredienteFactory::mussarela()),
                IngredienteEscolhidoFactory::padrao(3, IngredienteFactory::calabresa()),
                IngredienteEscolhidoFactory::adicional(4, IngredienteFactory::bacon(), 899),
            ],
        );

        $this->saborEscolhidoCalabresaMeiaPizza = new SaborEscolhido(
            id: 4,
            pizza: $this->pizzaCalabresa,
            ingredientesEscolhidos: [
                IngredienteEscolhidoFactory::padrao(1, IngredienteFactory::molho()),
                IngredienteEscolhidoFactory::padrao(2, IngredienteFactory::mussarela()),
                IngredienteEscolhidoFactory::padrao(3, IngredienteFactory::calabresa()),
                IngredienteEscolhidoFactory::adicional(4, IngredienteFactory::bacon(), 450),
            ],
        );

        $this->saborEscolhidoMargheritaMeiaPizza = new SaborEscolhido(
            id: 2,
            pizza: $this->pizzaMargherita,
            ingredientesEscolhidos: [
                IngredienteEscolhidoFactory::padrao(1, IngredienteFactory::molho()),
                IngredienteEscolhidoFactory::padrao(2, IngredienteFactory::mussarela()),
                IngredienteEscolhidoFactory::padrao(3, IngredienteFactory::catupiry()),
                IngredienteEscolhidoFactory::padrao(4, IngredienteFactory::manjericao()),
                IngredienteEscolhidoFactory::adicional(5, IngredienteFactory::bacon(), 450),
            ],
        );

        $this->itemPizzaCalabresa = new ItemPizza(
            id: ItemPedido::MENOR_ID_PERMITIDO,
            quantidade: ItemPedido::QUANTIDADE_MINIMA_PERMITIDA,
            tamanhoPizza: TamanhoPizza::MEDIA,
            saboresEscolhidos: [$this->saborEscolhidoCalabresa],
        );

        $this->itemPizzaCalabresaComMargherita = new ItemPizza(
            id: ItemPedido::MENOR_ID_PERMITIDO,
            quantidade: ItemPedido::QUANTIDADE_MINIMA_PERMITIDA,
            tamanhoPizza: TamanhoPizza::GRANDE,
            saboresEscolhidos: [$this->saborEscolhidoMargheritaMeiaPizza, $this->saborEscolhidoCalabresaMeiaPizza],
        );
    });

    describe('calcularDescontos()', function () {

        it('calcula corretamente o desconto quando não há itens válidos para a promoção', function () {
            $promocoes = [
                new Promocao(
                    id: 1,
                    descricao: 'Promoção de teste',
                    tamanhoPizza: TamanhoPizza::PEQUENA,
                    quantidadeMinima: 2,
                    percentualDesconto: new Percentual(10),
                    dataInicio: null,
                    dataFim: null,
                ),
            ];

            $descontos = new CalculadoraDescontoPromocao()->calcularDescontos(
                itensPizza: [$this->itemPizzaCalabresa, $this->itemPizzaCalabresaComMargherita],
                promocoes: $promocoes,
            );

            expect($descontos)->toHaveLength(0);
            expect($descontos)->toBe([]);
        });

        it('calcula corretamente o desconto quando não há promoções', function () {
            $descontos = new CalculadoraDescontoPromocao()->calcularDescontos(
                itensPizza: [$this->itemPizzaCalabresa, $this->itemPizzaCalabresaComMargherita],
                promocoes: [],
            );

            expect($descontos)->toHaveLength(0);
            expect($descontos)->toBe([]);
        });

        it('calcula corretamente o desconto quando todos os itens são válidos para a promoção', function () {
            $promocoes = [
                new Promocao(
                    id: 1,
                    descricao: 'Promoção de teste',
                    tamanhoPizza: TamanhoPizza::MEDIA,
                    quantidadeMinima: 1,
                    percentualDesconto: new Percentual(10),
                    dataInicio: null,
                    dataFim: null,
                ),
            ];

            $descontos = new CalculadoraDescontoPromocao()->calcularDescontos(
                itensPizza: [$this->itemPizzaCalabresa],
                promocoes: $promocoes,
            );

            expect($descontos)->toHaveLength(1);
            expect($descontos[0]->valorDesconto->centavos)->toBe(419);
        });

        it('calcula corretamente o desconto quando há alguns itens válidos para a promoção', function () {
            $promocoes = [
                new Promocao(
                    id: 1,
                    descricao: 'Promoção de teste',
                    tamanhoPizza: TamanhoPizza::GRANDE,
                    quantidadeMinima: 1,
                    percentualDesconto: new Percentual(10),
                    dataInicio: null,
                    dataFim: null,
                ),
            ];

            $descontos = new CalculadoraDescontoPromocao()->calcularDescontos(
                itensPizza: [$this->itemPizzaCalabresa, $this->itemPizzaCalabresaComMargherita],
                promocoes: $promocoes,
            );

            expect($descontos)->toHaveLength(1);
            expect($descontos[0]->valorDesconto->centavos)->toBe(540);
        });

        it('calcula corretamente o desconto quando a quantidade mínima para a promoção não é atendida', function () {
            $promocoes = [
                new Promocao(
                    id: 1,
                    descricao: 'Promoção de teste',
                    tamanhoPizza: TamanhoPizza::MEDIA,
                    quantidadeMinima: 2,
                    percentualDesconto: new Percentual(10),
                    dataInicio: null,
                    dataFim: null,
                ),
            ];

            $descontos = new CalculadoraDescontoPromocao()->calcularDescontos(
                itensPizza: [$this->itemPizzaCalabresa],
                promocoes: $promocoes,
            );

            expect($descontos)->toHaveLength(0);
            expect($descontos)->toBe([]);
        });

        it('calcula corretamente o desconto quando a quantidade mínima para a promoção é atendida', function () {
            $promocoes = [
                new Promocao(
                    id: 1,
                    descricao: 'Promoção de teste',
                    tamanhoPizza: TamanhoPizza::MEDIA,
                    quantidadeMinima: 1,
                    percentualDesconto: new Percentual(10),
                    dataInicio: null,
                    dataFim: null,
                ),
            ];

            $descontos = new CalculadoraDescontoPromocao()->calcularDescontos(
                itensPizza: [$this->itemPizzaCalabresa, $this->itemPizzaCalabresaComMargherita],
                promocoes: $promocoes,
            );

            expect($descontos)->toHaveLength(1);
            expect($descontos[0]->valorDesconto->centavos)->toBe(419);
        });

    });

});
