<?php

describe('CalculadoraDescontoPromocao', function () {

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
