<?php

describe('GeradorBeneficioFidelidade', function () {

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

        $this->pedido = new Pedido(
            id: 1,
            data: new DateTime(),
            tipoAtendimento: TipoAtendimento::RETIRADA,
            status: StatusPedido::EM_PREPARACAO,
            cliente: null,
            itensPedido: $this->itensPedido,
        );
    });

    describe('gerarBeneficioFidelidade()', function () {

        it('não gera benefício se o valor do pedido for menor que o mínimo', function () {
            var_dump($this->pedido->calcularValorTotal()->centavos); // Adicione esta linha para depuração
            $beneficio = new GeradorBeneficioFidelidade()->gerarBeneficioFidelidade(
                pedido: $this->pedido,
                configuracaoFidelidade: new ConfiguracaoFidelidade(
                    id: 1,
                    valorMinimo: new Dinheiro(10979),
                    percentual: new Percentual(10),
                    validadeEmDias: 30,
                ),
            );

            expect($beneficio)->toBeNull();
        });

        it('gera benefício se o valor do pedido for maior ou igual ao mínimo', function () {
            $beneficio = new GeradorBeneficioFidelidade()->gerarBeneficioFidelidade(
                pedido: $this->pedido,
                configuracaoFidelidade: new ConfiguracaoFidelidade(
                    id: 1,
                    valorMinimo: new Dinheiro(10978),
                    percentual: new Percentual(10),
                    validadeEmDias: 30,
                ),
            );

            expect($beneficio)->toBeAnInstanceOf(BeneficioFidelidade::class);
            expect($beneficio->percentualDesconto->valor)->toEqual(10);
            expect($beneficio->validade)->toBeAnInstanceOf(DateTime::class);
            expect($beneficio->validade->format('Y-m-d H:i:s'))->toBe(new DateTime('+30 days')->format('Y-m-d H:i:s'));
            expect($beneficio->utilizado)->toBe(false);
        });

    });

});
