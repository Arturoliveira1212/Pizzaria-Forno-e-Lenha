<?php

describe('Pedido', function () {

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

    $this->itensPedido = [
        $this->itemSimplesCocaCola,
        $this->itemSimplesSorvete,
        $this->itemPizzaCalabresa,
        $this->itemPizzaCalabresaComMargherita,
    ];

    $this->beneficoFidelidade = new BeneficioFidelidade(
        id: 1,
        percentualDesconto: new Percentual(10),
        dataConcessao: new DateTime('2024-01-01'),
        validade: new DateTime('2024-12-31'),
        utilizado: false,
    );

    $this->endereco = new Endereco(
        id: 1,
        logradouro: 'Rua Viviane Verly Pereira',
        numero: '53',
        bairro: 'Merelim',
        cidade: 'Monnerat',
        estado: 'Rio de Janeiro',
        uf: 'RJ',
    );

    $this->cliente = new Cliente(
        id: 1,
        nome: 'João da Silva',
        telefone: new Telefone('22', '99999-9999'),
        cpf: new Cpf('123.456.789-00'),
        endereco: $this->endereco,
        beneficioFidelidade: $this->beneficoFidelidade,
    );

    $this->descontoFidelidade = new DescontoFidelidade(
        id: DescontoPedido::MENOR_ID_PERMITIDO,
        valorDesconto: new Dinheiro(1098),
        beneficioFidelidade: $this->beneficoFidelidade,
    );

    $this->descontoFidelidadeAposPromocao = new DescontoFidelidade(
        id: DescontoPedido::MENOR_ID_PERMITIDO,
        valorDesconto: new Dinheiro(1056),
        beneficioFidelidade: $this->beneficoFidelidade,
    );

    $this->descontoPromocao = new DescontoPromocao(
        id: DescontoPedido::MENOR_ID_PERMITIDO,
        valorDesconto: new Dinheiro(419),
        promocao: new Promocao(
            id: 1,
            descricao: 'Promoção Teste',
            quantidadeMinima: 1,
            tamanhoPizza: TamanhoPizza::MEDIA,
            percentualDesconto: new Percentual(10),
            dataInicio: new DateTime('2024-01-01'),
            dataFim: new DateTime('2024-12-31'),
        ),
    );

    $this->entregador = new Entregador(
        id: 1,
        nome: 'João Coves',
        telefone: new Telefone('22', '99999-9999'),
        disponivel: true,
    );

    $this->entrega = new Entrega(
        id: 1,
        endereco: $this->endereco,
        entregador: $this->entregador,
        dataSaida: new DateTime('2024-01-01 10:00:00'),
        dataChegada: new DateTime('2024-01-01 10:30:00'),
        taxa: new Dinheiro(500),
    );

    $this->cancelamento = new Cancelamento(
        id: 1,
        motivo: 'Cliente desistiu do pedido',
        data: new DateTime('2024-01-01 11:00:00'),
    );

    describe('validar()', function () {

        it('lança exceção quando o identificador é menor que o mínimo permitido', function () {
            expect(function () {
                $id = Pedido::MENOR_ID_PERMITIDO - 1;

                return new Pedido(
                    id: $id,
                    data: new DateTime(),
                    tipoAtendimento: TipoAtendimento::BALCAO,
                    cliente: null,
                    itensPedido: $this->itensPedido,
                    status: StatusPedido::RECEBIDO,
                );
            })->toThrow(new DominioException('O identificador do ingrediente escolhido deve ser maior que ' . Pedido::MENOR_ID_PERMITIDO . '.'));
        });

        it('lança exceção quando a data do pedido é futura', function () {
            expect(function () {
                return new Pedido(
                    id: Pedido::MENOR_ID_PERMITIDO,
                    data: new DateTime('+1 day'),
                    tipoAtendimento: TipoAtendimento::BALCAO,
                    cliente: null,
                    itensPedido: $this->itensPedido,
                    status: StatusPedido::RECEBIDO,
                );
            })->toThrow(new DominioException('A data do pedido não pode ser futura.'));
        });

        it('lança exceção quando o tipo de atendimento exige cliente e o cliente não é informado', function () {
            expect(function () {
                return new Pedido(
                    id: Pedido::MENOR_ID_PERMITIDO,
                    data: new DateTime(),
                    tipoAtendimento: TipoAtendimento::ENTREGA,
                    cliente: null,
                    itensPedido: $this->itensPedido,
                    status: StatusPedido::RECEBIDO,
                );
            })->toThrow(new DominioException('O pedido exige um cliente.'));
        });

        it('lança exceção quando o pedido não possui itens', function () {
            expect(function () {
                return new Pedido(
                    id: Pedido::MENOR_ID_PERMITIDO,
                    data: new DateTime(),
                    tipoAtendimento: TipoAtendimento::BALCAO,
                    cliente: null,
                    itensPedido: [],
                    status: StatusPedido::RECEBIDO,
                );
            })->toThrow(new DominioException('O pedido deve conter pelo menos um item.'));
        });

        it('lança exceção quando o desconto de fidelidade é informado e o cliente não é informado', function () {
            expect(function () {
                return new Pedido(
                    id: Pedido::MENOR_ID_PERMITIDO,
                    data: new DateTime(),
                    tipoAtendimento: TipoAtendimento::BALCAO,
                    cliente: null,
                    itensPedido: $this->itensPedido,
                    status: StatusPedido::RECEBIDO,
                    descontoFidelidade: $this->descontoFidelidade,
                );
            })->toThrow(new DominioException('O desconto de fidelidade exige um cliente.'));
        });

        it('lança exceção quando a entrega é informada e o cliente não é informado', function () {
            expect(function () {
                return new Pedido(
                    id: Pedido::MENOR_ID_PERMITIDO,
                    data: new DateTime(),
                    tipoAtendimento: TipoAtendimento::BALCAO,
                    cliente: null,
                    itensPedido: $this->itensPedido,
                    status: StatusPedido::RECEBIDO,
                    entrega: $this->entrega,
                );
            })->toThrow(new DominioException('A entrega exige um cliente.'));
        });

        it('lança exceção quando o cancelamento é informado e o status do pedido não é CANCELADO', function () {
            expect(function () {
                return new Pedido(
                    id: Pedido::MENOR_ID_PERMITIDO,
                    data: new DateTime(),
                    tipoAtendimento: TipoAtendimento::BALCAO,
                    cliente: null,
                    itensPedido: $this->itensPedido,
                    status: StatusPedido::RECEBIDO,
                    cancelamento: $this->cancelamento,
                );
            })->toThrow(new DominioException('O cancelamento só pode ser registrado se o status do pedido for CANCELADO.'));
        });

        it('cria o pedido quando todos os campos são válidos', function () {
            $pedido = new Pedido(
                id: Pedido::MENOR_ID_PERMITIDO,
                data: new DateTime(),
                tipoAtendimento: TipoAtendimento::BALCAO,
                cliente: null,
                itensPedido: $this->itensPedido,
                status: StatusPedido::RECEBIDO,
            );

            expect($pedido->id)->toBe(Pedido::MENOR_ID_PERMITIDO);
            expect($pedido->data)->toBeAnInstanceOf(DateTime::class);
            expect($pedido->tipoAtendimento)->toBe(TipoAtendimento::BALCAO);
            expect($pedido->cliente)->toBeNull();
            expect($pedido->status)->toBe(StatusPedido::RECEBIDO);
            expect($pedido->itensPedido)->toHaveLength(4);
            expect($pedido->itensPedido[0])->toBe($this->itemSimplesCocaCola);
            expect($pedido->itensPedido[1])->toBe($this->itemSimplesSorvete);
            expect($pedido->itensPedido[2])->toBe($this->itemPizzaCalabresa);
            expect($pedido->itensPedido[3])->toBe($this->itemPizzaCalabresaComMargherita);
        });

    });

    describe('calcularValorTotal()', function () {

        it('calcula corretamente o valor total do pedido sem descontos', function () {
            $pedido = new Pedido(
                id: Pedido::MENOR_ID_PERMITIDO,
                data: new DateTime(),
                tipoAtendimento: TipoAtendimento::BALCAO,
                cliente: null,
                itensPedido: $this->itensPedido,
                status: StatusPedido::RECEBIDO,
            );

            expect($pedido->calcularValorTotal()->centavos)->toBe(10976);
        });

        it('calcula corretamente o valor total do pedido com desconto de fidelidade', function () {
            $pedido = new Pedido(
                id: Pedido::MENOR_ID_PERMITIDO,
                data: new DateTime(),
                tipoAtendimento: TipoAtendimento::BALCAO,
                cliente: $this->cliente,
                itensPedido: $this->itensPedido,
                status: StatusPedido::RECEBIDO,
                descontoFidelidade: $this->descontoFidelidade,
            );

            expect($pedido->calcularValorTotal()->centavos)->toBe(9878);
        });

        it('calcula corretamente o valor total do pedido com desconto de promoção', function () {
            $pedido = new Pedido(
                id: Pedido::MENOR_ID_PERMITIDO,
                data: new DateTime(),
                tipoAtendimento: TipoAtendimento::BALCAO,
                cliente: null,
                itensPedido: $this->itensPedido,
                status: StatusPedido::RECEBIDO,
                descontosPromocao: [$this->descontoPromocao],
            );

            expect($pedido->calcularValorTotal()->centavos)->toBe(10557);
        });

        it('calcula corretamente o valor total do pedido com desconto de fidelidade e desconto de promoção', function () {
            $pedido = new Pedido(
                id: Pedido::MENOR_ID_PERMITIDO,
                data: new DateTime(),
                tipoAtendimento: TipoAtendimento::BALCAO,
                cliente: $this->cliente,
                itensPedido: $this->itensPedido,
                status: StatusPedido::RECEBIDO,
                descontoFidelidade: $this->descontoFidelidadeAposPromocao,
                descontosPromocao: [$this->descontoPromocao],
            );

            expect($pedido->calcularValorTotal()->centavos)->toBe(9501);
        });

    });

});
