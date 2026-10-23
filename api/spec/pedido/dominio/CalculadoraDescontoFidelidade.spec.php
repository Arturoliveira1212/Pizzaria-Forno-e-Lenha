<?php

describe('CalculadoraDescontoFidelidade', function () {

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

        $this->saborEscolhidoMargherita = new SaborEscolhido(
            id: 2,
            pizza: $this->pizzaMargherita,
            ingredientesEscolhidos: [
                IngredienteEscolhidoFactory::padrao(1, IngredienteFactory::molho()),
                IngredienteEscolhidoFactory::padrao(2, IngredienteFactory::mussarela()),
                IngredienteEscolhidoFactory::padrao(3, IngredienteFactory::catupiry()),
                IngredienteEscolhidoFactory::padrao(4, IngredienteFactory::manjericao()),
                IngredienteEscolhidoFactory::adicional(5, IngredienteFactory::bacon(), 899),
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

        $this->itemSimplesCocaCola = new ItemSimples(
            id: ItemPedido::MENOR_ID_PERMITIDO,
            quantidade: ItemPedido::QUANTIDADE_MINIMA_PERMITIDA,
            produtoSimples: ProdutoSimplesFactory::cocaCola(),
        );

        $this->itemSimplesSorvete = new ItemSimples(
            id: ItemPedido::MENOR_ID_PERMITIDO,
            quantidade: ItemPedido::QUANTIDADE_MINIMA_PERMITIDA,
            produtoSimples: ProdutoSimplesFactory::sorvete(),
        );

        $this->itensPedido = [$this->itemPizzaCalabresa, $this->itemPizzaCalabresaComMargherita, $this->itemSimplesCocaCola, $this->itemSimplesSorvete];

        $this->beneficioFidelidade = new BeneficioFidelidade(
            id: 1,
            percentualDesconto: new Percentual(10),
            dataConcessao: new DateTime('2024-01-01 00:00:00'),
            validade: new DateTime()->modify('+30 days'),
            utilizado: false,
        );
    });

    describe('calcularDesconto()', function () {

        it('lança exceção quando não tem itens pedido', function () {
            expect(function () {
                new CalculadoraDescontoFidelidade()->calcularDesconto([], $this->beneficioFidelidade, Dinheiro::zero());
            })->toThrow(new DominioException('O valor do desconto não pode ser zero.'));
        });

        it('calcula corretamente o desconto de fidelidade (sem promoção)', function () {
            $descontoFidelidade = new CalculadoraDescontoFidelidade()->calcularDesconto(
                itensPedido: $this->itensPedido,
                beneficioFidelidade: $this->beneficioFidelidade,
                valorTotalDescontoPromocao: Dinheiro::zero(),
            );

            expect($descontoFidelidade->valorDesconto->centavos)->toBe(1098);
        });

        it('calcula corretamente o desconto de fidelidade (com promoção)', function () {
            $descontoFidelidade = new CalculadoraDescontoFidelidade()->calcularDesconto(
                itensPedido: $this->itensPedido,
                beneficioFidelidade: $this->beneficioFidelidade,
                valorTotalDescontoPromocao: new Dinheiro(578),
            );

            expect($descontoFidelidade->valorDesconto->centavos)->toBe(1040);
        });

    });
});
