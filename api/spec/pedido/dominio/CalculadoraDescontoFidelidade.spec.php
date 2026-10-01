<?php

describe('CalculadoraDescontoFidelidade', function () {

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
                    new IngredienteAdicional(id: 1, ingrediente: $this->bacon, preco: new Dinheiro(899)),
                ],
                precosPorTamanho: [
                    new PrecoPorTamanho(id: 1, tamanhoPizza: TamanhoPizza::PEQUENA, preco: new Dinheiro(2290)),
                    new PrecoPorTamanho(id: 2, tamanhoPizza: TamanhoPizza::MEDIA, preco: new Dinheiro(3290)),
                    new PrecoPorTamanho(id: 3, tamanhoPizza: TamanhoPizza::GRANDE, preco: new Dinheiro(4399)),
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
                    id: 3,
                    ingrediente: $this->bacon,
                    papel: PapelIngrediente::ADICIONAL,
                    custoAdicional: new Dinheiro(899),
                ),
            ],
        );

        $this->saborEscolhidoMargherita = new SaborEscolhido(
            id: 2,
            pizza: new Pizza(
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
                    id: 3,
                    ingrediente: $this->bacon,
                    papel: PapelIngrediente::ADICIONAL,
                    custoAdicional: new Dinheiro(899),
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
            saboresEscolhidos: [$this->saborEscolhidoMargherita, $this->saborEscolhidoCalabresa],
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
