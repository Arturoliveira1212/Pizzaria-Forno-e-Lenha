<?php

use Kahlan\Plugin\Double;

describe('GestorPedido', function () {

    beforeEach(function () {
        $this->unidadeTrabalho = Double::instance(['implements' => UnidadeTrabalho::class]);
        $this->repositorioPedido = Double::instance(['implements' => RepositorioPedido::class]);
        $this->repositorioCliente = Double::instance(['implements' => RepositorioCliente::class]);
        $this->repositorioPizza = Double::instance(['implements' => RepositorioPizza::class]);
        $this->repositorioProdutoSimples = Double::instance(['implements' => RepositorioProdutoSimples::class]);
        $this->repositorioIngrediente = Double::instance(['implements' => RepositorioIngrediente::class]);
        $this->repositorioPromocao = Double::instance(['implements' => RepositorioPromocao::class]);
        $this->repositorioConfiguracaoFidelidade = Double::instance(['implements' => RepositorioConfiguracaoFidelidade::class]);

        $this->gestorPedido = new GestorPedido(
            unidadeTrabalho: $this->unidadeTrabalho,
            repositorioPedido: $this->repositorioPedido,
            repositorioCliente: $this->repositorioCliente,
            repositorioPizza: $this->repositorioPizza,
            repositorioProdutoSimples: $this->repositorioProdutoSimples,
            repositorioIngrediente: $this->repositorioIngrediente,
            repositorioPromocao: $this->repositorioPromocao,
            repositorioConfiguracaoFidelidade: $this->repositorioConfiguracaoFidelidade,
        );
    });

    beforeAll(function () {
        $this->pizza = new Pizza(
            id: 1,
            nome: 'Pizza Teste',
            descricao: 'Descrição da pizza teste',
            precosPorTamanho: [
                new PrecoPorTamanho(id: 1, tamanhoPizza: TamanhoPizza::MEDIA, preco: new Dinheiro(30.0)),
            ],
            ingredientesPadrao: [],
            adicionais: [],
            ingredientesSubstitutos: [],
        );
        $this->produtoSimples = new ProdutoSimples(
            id: 1,
            nome: 'Coca-Cola 2L',
            descricao: 'Refrigerante de cola',
            tipo: TipoProdutoSimples::BEBIDA,
            preco: new Dinheiro(10.0),
        );
        $this->queijo = new Ingrediente(1, 'Queijo');
        $this->promocao = new Promocao(
            id: 1,
            descricao: 'Promoção Teste',
            quantidadeMinima: 1,
            tamanhoPizza: TamanhoPizza::MEDIA,
            percentualDesconto: new Percentual(10.0),
            dataInicio: null,
            dataFim: null,
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
        $this->cpf = new Cpf('123.456.789-00');
        $this->telefone = new Telefone('22', '99999-9999');
    });

    describe('registrarPedido()', function () {

        describe('TipoAtendimento', function () {

            it('lança exceção quando o tipo atendimento é inválido', function () {
                $tipoQueNaoExiste = 'tipo_que_nao_existe';
                expect(function () use ($tipoQueNaoExiste) {

                    $this->gestorPedido->registrarPedido(
                        new PedidoParaCadastro(
                            tipo: $tipoQueNaoExiste,
                            idCliente: 'dasdsadasdsadas',
                            itensPedido: [],
                        ),
                    );
                })->toThrow(new DominioException("Tipo de atendimento inválido: {$tipoQueNaoExiste}."));
            });
        });

        describe('ItemPizza', function () {

            it('lança exceção quando o tamanho da pizza é inválido', function () {
                $tamanhoQueNaoExiste = 'tamanho_que_nao_existe';
                expect(function () use ($tamanhoQueNaoExiste) {

                    $this->gestorPedido->registrarPedido(
                        new PedidoParaCadastro(
                            tipo: TipoAtendimento::BALCAO->value,
                            idCliente: 'dasdsadasdsadas',
                            itensPedido: [
                                new ItemPizzaParaCadastro(
                                    tamanho: $tamanhoQueNaoExiste,
                                    quantidade: 1,
                                    saboresEscolhidos: [
                                        new SaborEscolhidoParaCadastro(
                                            idPizza: 1,
                                            ingredientesEscolhidos: [],
                                        ),
                                    ],
                                ),
                            ],
                        ),
                    );
                })->toThrow(new DominioException("Tamanho de pizza inválido: {$tamanhoQueNaoExiste}."));
            });

        });

        describe('SaborEscolhido', function () {

            it('lança exceção quando a pizza não existe', function () {
                allow($this->repositorioPizza)->toReceive('obterPizzaPorId')->andReturn(null);

                $idInexistente = 999;

                expect(function () use ($idInexistente) {

                    $this->gestorPedido->registrarPedido(
                        new PedidoParaCadastro(
                            tipo: TipoAtendimento::BALCAO->value,
                            idCliente: 'dasdsadasdsadas',
                            itensPedido: [
                                new ItemPizzaParaCadastro(
                                    tamanho: TamanhoPizza::MEDIA->value,
                                    quantidade: 1,
                                    saboresEscolhidos: [
                                        new SaborEscolhidoParaCadastro(
                                            idPizza: 999,
                                            ingredientesEscolhidos: [],
                                        ),
                                    ],
                                ),
                            ],
                        ),
                    );
                })->toThrow(new DominioException("Pizza (ID {$idInexistente}) não encontrada."));

            });

        });

        describe('IngredienteEscolhido', function () {

            it('lança exceção quando o ingrediente não existe', function () {
                allow($this->repositorioPizza)->toReceive('obterPizzaPorId')->andReturn($this->pizza);
                allow($this->repositorioIngrediente)->toReceive('obterIngredientePorId')->andReturn(null);

                $idInexistente = 999;

                expect(function () use ($idInexistente) {
                    $this->gestorPedido->registrarPedido(
                        new PedidoParaCadastro(
                            tipo: TipoAtendimento::BALCAO->value,
                            idCliente: 'dasdsadasdsadas',
                            itensPedido: [
                                new ItemPizzaParaCadastro(
                                    tamanho: TamanhoPizza::MEDIA->value,
                                    quantidade: 1,
                                    saboresEscolhidos: [
                                        new SaborEscolhidoParaCadastro(
                                            idPizza: 1,
                                            ingredientesEscolhidos: [
                                                new IngredienteEscolhidoParaCadastro(
                                                    idIngrediente: $idInexistente,
                                                    papel: PapelIngrediente::PADRAO->value,
                                                ),
                                            ],
                                        ),
                                    ],
                                ),
                            ],
                        ),
                    );
                })->toThrow(new DominioException("Ingrediente (ID {$idInexistente}) não encontrado."));
            });

            it('lança exceção quando o papel do ingrediente é inválido', function () {
                allow($this->repositorioPizza)->toReceive('obterPizzaPorId')->andReturn($this->pizza);
                allow($this->repositorioIngrediente)->toReceive('obterIngredientePorId')->andReturn($this->queijo);

                $papelQueNaoExiste = 'papel_que_nao_existe';

                expect(function () use ($papelQueNaoExiste) {
                    $this->gestorPedido->registrarPedido(
                        new PedidoParaCadastro(
                            tipo: TipoAtendimento::BALCAO->value,
                            idCliente: 'dasdsadasdsadas',
                            itensPedido: [
                                new ItemPizzaParaCadastro(
                                    tamanho: TamanhoPizza::MEDIA->value,
                                    quantidade: 1,
                                    saboresEscolhidos: [
                                        new SaborEscolhidoParaCadastro(
                                            idPizza: 1,
                                            ingredientesEscolhidos: [
                                                new IngredienteEscolhidoParaCadastro(
                                                    idIngrediente: 1,
                                                    papel: $papelQueNaoExiste,
                                                ),
                                            ],
                                        ),
                                    ],
                                ),
                            ],
                        ),
                    );
                })->toThrow(new DominioException("Papel do ingrediente inválido: {$papelQueNaoExiste}."));
            });

        });

        describe('ItemSimples', function () {

            it('lança exceção quando o produto simples não existe', function () {
                allow($this->repositorioProdutoSimples)->toReceive('obterProdutoSimplesPorId')->andReturn(null);

                $idInexistente = 999;

                expect(function () use ($idInexistente) {
                    $this->gestorPedido->registrarPedido(
                        new PedidoParaCadastro(
                            tipo: TipoAtendimento::BALCAO->value,
                            idCliente: 'dasdsadasdsadas',
                            itensPedido: [
                                new ItemSimplesParaCadastro(
                                    idProduto: $idInexistente,
                                    quantidade: 1,
                                ),
                            ],
                        ),
                    );
                })->toThrow(new DominioException("Produto simples (ID {$idInexistente}) não encontrado."));
            });

        });

        describe('DescontoPromocao', function () {

            it('cria pedido com descontos de promoção quando existem promoções vigentes', function () {
                allow($this->repositorioPromocao)->toReceive('obterPromocoes')->andReturn([$this->promocao]);
                allow($this->repositorioProdutoSimples)->toReceive('obterProdutoSimplesPorId')->andReturn(null);
                allow($this->repositorioPizza)->toReceive('obterPizzaPorId')->andReturn($this->pizza);
                allow($this->repositorioCliente)->toReceive('obterClientePorId')->andReturn(null);

                $pedido = $this->gestorPedido->registrarPedido(
                    new PedidoParaCadastro(
                        tipo: TipoAtendimento::BALCAO->value,
                        idCliente: '',
                        itensPedido: [
                            new ItemPizzaParaCadastro(
                                tamanho: TamanhoPizza::MEDIA->value,
                                quantidade: 1,
                                saboresEscolhidos: [
                                    new SaborEscolhidoParaCadastro(
                                        idPizza: 1,
                                        ingredientesEscolhidos: [],
                                    ),
                                ],
                            ),
                        ],
                    ),
                );

                expect($pedido->descontosPromocao)->toHaveLength(1);
                expect($pedido->descontosPromocao[0]->promocao->id)->toBe(1);
            });

            it('cria pedido sem descontos de promoção quando não existem promoções vigentes', function () {
                allow($this->repositorioPromocao)->toReceive('obterPromocoes')->andReturn([]);
                allow($this->repositorioProdutoSimples)->toReceive('obterProdutoSimplesPorId')->andReturn(null);
                allow($this->repositorioPizza)->toReceive('obterPizzaPorId')->andReturn($this->pizza);
                allow($this->repositorioCliente)->toReceive('obterClientePorId')->andReturn(null);

                $pedido = $this->gestorPedido->registrarPedido(
                    new PedidoParaCadastro(
                        tipo: TipoAtendimento::BALCAO->value,
                        idCliente: '',
                        itensPedido: [
                            new ItemPizzaParaCadastro(
                                tamanho: TamanhoPizza::MEDIA->value,
                                quantidade: 1,
                                saboresEscolhidos: [
                                    new SaborEscolhidoParaCadastro(
                                        idPizza: 1,
                                        ingredientesEscolhidos: [],
                                    ),
                                ],
                            ),
                        ],
                    ),
                );

                expect($pedido->descontosPromocao)->toHaveLength(0);
            });

        });

        describe('DescontoFidelidade', function () {

            it('cria pedido com desconto de fidelidade quando o cliente tem beneficio valido', function () {
                allow($this->repositorioCliente)->toReceive('obterClientePorId')->andReturn(
                    $this->cliente = new Cliente(
                        id: 1,
                        nome: 'João da Silva',
                        telefone: $this->telefone,
                        cpf: $this->cpf,
                        endereco: $this->endereco,
                        beneficioFidelidade: new BeneficioFidelidade(
                            id: 1,
                            percentualDesconto: new Percentual(10),
                            dataConcessao: new DateTime('2024-01-01'),
                            validade: new DateTime()->modify('+30 days'),
                            utilizado: false,
                        ),
                    ),
                );
                allow($this->repositorioPromocao)->toReceive('obterPromocoes')->andReturn([]);
                allow($this->repositorioProdutoSimples)->toReceive('obterProdutoSimplesPorId')->andReturn(null);
                allow($this->repositorioPizza)->toReceive('obterPizzaPorId')->andReturn($this->pizza);

                $pedido = $this->gestorPedido->registrarPedido(
                    new PedidoParaCadastro(
                        tipo: TipoAtendimento::BALCAO->value,
                        idCliente: 'lkjn23rij23jil234jkb',
                        itensPedido: [
                            new ItemPizzaParaCadastro(
                                tamanho: TamanhoPizza::MEDIA->value,
                                quantidade: 1,
                                saboresEscolhidos: [
                                    new SaborEscolhidoParaCadastro(
                                        idPizza: 1,
                                        ingredientesEscolhidos: [],
                                    ),
                                ],
                            ),
                        ],
                    ),
                );

                expect($pedido->descontoFidelidade->beneficioFidelidade->id)->toBe(1);
            });

            it('cria pedido sem desconto de fidelidade quando o cliente não tem beneficio', function () {
                allow($this->repositorioCliente)->toReceive('obterClientePorId')->andReturn(
                    $this->cliente = new Cliente(
                        id: 1,
                        nome: 'João da Silva',
                        telefone: $this->telefone,
                        cpf: $this->cpf,
                        endereco: $this->endereco,
                        beneficioFidelidade: null,
                    ),
                );
                allow($this->repositorioPromocao)->toReceive('obterPromocoes')->andReturn([]);
                allow($this->repositorioProdutoSimples)->toReceive('obterProdutoSimplesPorId')->andReturn(null);
                allow($this->repositorioPizza)->toReceive('obterPizzaPorId')->andReturn($this->pizza);

                $pedido = $this->gestorPedido->registrarPedido(
                    new PedidoParaCadastro(
                        tipo: TipoAtendimento::BALCAO->value,
                        idCliente: 'lkjn23rij23jil234jkb',
                        itensPedido: [
                            new ItemPizzaParaCadastro(
                                tamanho: TamanhoPizza::MEDIA->value,
                                quantidade: 1,
                                saboresEscolhidos: [
                                    new SaborEscolhidoParaCadastro(
                                        idPizza: 1,
                                        ingredientesEscolhidos: [],
                                    ),
                                ],
                            ),
                        ],
                    ),
                );

                expect($pedido->descontoFidelidade)->toBe(null);
            });

            it('cria pedido sem desconto de fidelidade quando o cliente tem beneficio inválido', function () {
                allow($this->repositorioCliente)->toReceive('obterClientePorId')->andReturn(
                    $this->cliente = new Cliente(
                        id: 1,
                        nome: 'João da Silva',
                        telefone: $this->telefone,
                        cpf: $this->cpf,
                        endereco: $this->endereco,
                        beneficioFidelidade: new BeneficioFidelidade(
                            id: 1,
                            percentualDesconto: new Percentual(10),
                            dataConcessao: new DateTime('2024-01-01'),
                            validade: new DateTime()->modify('-1 day'),
                            utilizado: false,
                        ),
                    ),
                );
                allow($this->repositorioPromocao)->toReceive('obterPromocoes')->andReturn([]);
                allow($this->repositorioProdutoSimples)->toReceive('obterProdutoSimplesPorId')->andReturn(null);
                allow($this->repositorioPizza)->toReceive('obterPizzaPorId')->andReturn($this->pizza);

                $pedido = $this->gestorPedido->registrarPedido(
                    new PedidoParaCadastro(
                        tipo: TipoAtendimento::BALCAO->value,
                        idCliente: 'lkjn23rij23jil234jkb',
                        itensPedido: [
                            new ItemPizzaParaCadastro(
                                tamanho: TamanhoPizza::MEDIA->value,
                                quantidade: 1,
                                saboresEscolhidos: [
                                    new SaborEscolhidoParaCadastro(
                                        idPizza: 1,
                                        ingredientesEscolhidos: [],
                                    ),
                                ],
                            ),
                        ],
                    ),
                );

                expect($pedido->descontoFidelidade)->toBe(null);
            });

            it('cria pedido sem desconto de fidelidade quando o cliente não existe', function () {
                allow($this->repositorioCliente)->toReceive('obterClientePorId')->andReturn(null);
                allow($this->repositorioPromocao)->toReceive('obterPromocoes')->andReturn([]);
                allow($this->repositorioProdutoSimples)->toReceive('obterProdutoSimplesPorId')->andReturn(null);
                allow($this->repositorioPizza)->toReceive('obterPizzaPorId')->andReturn($this->pizza);

                $pedido = $this->gestorPedido->registrarPedido(
                    new PedidoParaCadastro(
                        tipo: TipoAtendimento::BALCAO->value,
                        idCliente: 'lkjn23rij23jil234jkb',
                        itensPedido: [
                            new ItemPizzaParaCadastro(
                                tamanho: TamanhoPizza::MEDIA->value,
                                quantidade: 1,
                                saboresEscolhidos: [
                                    new SaborEscolhidoParaCadastro(
                                        idPizza: 1,
                                        ingredientesEscolhidos: [],
                                    ),
                                ],
                            ),
                        ],
                    ),
                );

                expect($pedido->descontoFidelidade)->toBe(null);
            });

        });

        describe('Todos os Campos', function () {

            it('cria pedido com todos os campos válidos', function () {
                allow($this->repositorioCliente)->toReceive('obterClientePorId')->andReturn(
                    $this->cliente = new Cliente(
                        id: 'lkjn23rij23jil234jkb',
                        nome: 'João da Silva',
                        telefone: $this->telefone,
                        cpf: $this->cpf,
                        endereco: $this->endereco,
                        beneficioFidelidade: null,
                    ),
                );
                allow($this->repositorioPromocao)->toReceive('obterPromocoes')->andReturn([]);
                allow($this->repositorioProdutoSimples)->toReceive('obterProdutoSimplesPorId')->andReturn($this->produtoSimples);
                allow($this->repositorioPizza)->toReceive('obterPizzaPorId')->andReturn($this->pizza);

                $pedido = $this->gestorPedido->registrarPedido(
                    new PedidoParaCadastro(
                        tipo: TipoAtendimento::BALCAO->value,
                        idCliente: 'lkjn23rij23jil234jkb',
                        itensPedido: [
                            new ItemPizzaParaCadastro(
                                tamanho: TamanhoPizza::MEDIA->value,
                                quantidade: 1,
                                saboresEscolhidos: [
                                    new SaborEscolhidoParaCadastro(
                                        idPizza: 1,
                                        ingredientesEscolhidos: [],
                                    ),
                                ],
                            ),
                            new ItemSimplesParaCadastro(
                                idProduto: 1,
                                quantidade: 2,
                            ),
                        ],
                    ),
                );

                expect($pedido)->toBeAnInstanceOf(Pedido::class);
                expect($pedido->data)->toBeAnInstanceOf(DateTime::class);
                expect($pedido->tipoAtendimento)->toBe(TipoAtendimento::BALCAO);
                expect($pedido->cliente->id)->toBe('lkjn23rij23jil234jkb');
                expect($pedido->itensPedido)->toHaveLength(2);
                expect($pedido->itensPedido[0]->tamanhoPizza)->toBe(TamanhoPizza::MEDIA);
                expect($pedido->itensPedido[1]->produtoSimples->id)->toBe(1);
                expect($pedido->itensPedido[1]->quantidade)->toBe(2);
            });

        });
    });

});
