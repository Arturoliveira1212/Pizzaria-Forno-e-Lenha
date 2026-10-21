<?php

describe('GeradorBeneficioFidelidade', function () {

    beforeAll(function () {
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

        $this->pizzaCalabresa = new Pizza(
            id: 1,
            nome: 'Calabresa',
            descricao: 'Descrição',
            ingredientesPadrao: [
                new IngredientePadrao(id: 1, ingrediente: $this->molho, removivel: false, substituivel: false),
                new IngredientePadrao(id: 2, ingrediente: $this->mussarela, removivel: false, substituivel: false),
                new IngredientePadrao(id: 3, ingrediente: $this->calabresa, removivel: true, substituivel: true),
            ],
            ingredientesSubstitutos: [$this->cebola, $this->milho],
            adicionais: [
                new IngredienteAdicional(id: 1, ingrediente: $this->bacon, preco: new Dinheiro(899)),
            ],
            precosPorTamanho: [
                new PrecoPorTamanho(id: 1, tamanhoPizza: TamanhoPizza::PEQUENA, preco: new Dinheiro(2290)),
                new PrecoPorTamanho(id: 2, tamanhoPizza: TamanhoPizza::MEDIA, preco: new Dinheiro(3290)),
                new PrecoPorTamanho(id: 3, tamanhoPizza: TamanhoPizza::GRANDE, preco: new Dinheiro(4399)),
            ],
        );

        $this->pizzaMargherita = new Pizza(
            id: 2,
            nome: 'Margherita',
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
                new IngredienteAdicional(id: 2, ingrediente: $this->calabresa, preco: new Dinheiro(799)),
            ],
            precosPorTamanho: [
                new PrecoPorTamanho(id: 1, tamanhoPizza: TamanhoPizza::PEQUENA, preco: new Dinheiro(2190)),
                new PrecoPorTamanho(id: 2, tamanhoPizza: TamanhoPizza::MEDIA, preco: new Dinheiro(3090)),
                new PrecoPorTamanho(id: 3, tamanhoPizza: TamanhoPizza::GRANDE, preco: new Dinheiro(4599)),
            ],
        );

        $this->saborEscolhidoCalabresa = new SaborEscolhido(
            id: 3,
            pizza: $this->pizzaCalabresa,
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
                    id: 3,
                    ingrediente: $this->bacon,
                    papel: PapelIngrediente::ADICIONAL,
                    custoAdicional: new Dinheiro(899),
                ),
            ],
        );

        $this->saborEscolhidoCalabresaMeiaPizza = new SaborEscolhido(
            id: 4,
            pizza: $this->pizzaCalabresa,
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
                    id: 3,
                    ingrediente: $this->bacon,
                    papel: PapelIngrediente::ADICIONAL,
                    custoAdicional: new Dinheiro(450),
                ),
            ],
        );

        $this->saborEscolhidoMargheritaMeiaPizza = new SaborEscolhido(
            id: 2,
            pizza: $this->pizzaMargherita,
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
                    id: 3,
                    ingrediente: $this->bacon,
                    papel: PapelIngrediente::ADICIONAL,
                    custoAdicional: new Dinheiro(450),
                ),
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
            produtoSimples: new ProdutoSimples(
                id: 1,
                nome: 'Coca-Cola',
                descricao: 'Refrigerante de cola',
                tipo: TipoProdutoSimples::BEBIDA,
                preco: new Dinheiro(590),
            ),
        );

        $this->itemSimplesSorvete = new ItemSimples(
            id: ItemPedido::MENOR_ID_PERMITIDO,
            quantidade: ItemPedido::QUANTIDADE_MINIMA_PERMITIDA,
            produtoSimples: new ProdutoSimples(
                id: 2,
                nome: 'Sorvete',
                descricao: 'Sorvete de chocolate',
                tipo: TipoProdutoSimples::SOBREMESA,
                preco: new Dinheiro(799),
            ),
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
