<?php

use Kahlan\Plugin\Double;

function capturarExcecao(callable $operacao): ?Throwable {
    try {
        $operacao();
    } catch (Throwable $e) {
        return $e;
    }

    return null;
}

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
        $this->repositorioMovimentacaoEstoque = Double::instance(['implements' => RepositorioMovimentacaoEstoque::class]);

        $this->gestorPedido = new GestorPedido(
            unidadeTrabalho: $this->unidadeTrabalho,
            repositorioPedido: $this->repositorioPedido,
            repositorioCliente: $this->repositorioCliente,
            repositorioPizza: $this->repositorioPizza,
            repositorioProdutoSimples: $this->repositorioProdutoSimples,
            repositorioIngrediente: $this->repositorioIngrediente,
            repositorioPromocao: $this->repositorioPromocao,
            repositorioConfiguracaoFidelidade: $this->repositorioConfiguracaoFidelidade,
            repositorioMovimentacaoEstoque: $this->repositorioMovimentacaoEstoque,
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

        $this->queijo = IngredienteFactory::mussarela();

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

                /** @var DominioException $e */
                $e = capturarExcecao(function () use ($tipoQueNaoExiste) {
                    $this->gestorPedido->registrarPedido(
                        new PedidoParaCadastro(
                            tipoAtendimento: $tipoQueNaoExiste,
                            idCliente: 1,
                            itensPedido: [],
                        ),
                    );
                });

                expect($e)->toBeAnInstanceOf(DominioException::class);
                expect($e->erros())->toBe(["Tipo de atendimento inválido: {$tipoQueNaoExiste}."]);
            });
        });

        describe('ItemPizza', function () {

            it('lança exceção quando o tamanho da pizza é inválido', function () {
                $tamanhoQueNaoExiste = 'tamanho_que_nao_existe';

                /** @var DominioException $e */
                $e = capturarExcecao(function () use ($tamanhoQueNaoExiste) {
                    $this->gestorPedido->registrarPedido(
                        new PedidoParaCadastro(
                            tipoAtendimento: TipoAtendimento::BALCAO->value,
                            idCliente: 1,
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
                });

                expect($e)->toBeAnInstanceOf(DominioException::class);
                expect($e->erros())->toBe(["Tamanho de pizza inválido: {$tamanhoQueNaoExiste}."]);
            });

        });

        describe('SaborEscolhido', function () {

            it('lança exceção quando a pizza não existe', function () {
                allow($this->repositorioPizza)->toReceive('obterPizzaPorId')->andReturn(null);

                $idInexistente = 999;

                /** @var DominioException $e */
                $e = capturarExcecao(function () use ($idInexistente) {
                    $this->gestorPedido->registrarPedido(
                        new PedidoParaCadastro(
                            tipoAtendimento: TipoAtendimento::BALCAO->value,
                            idCliente: 1,
                            itensPedido: [
                                new ItemPizzaParaCadastro(
                                    tamanho: TamanhoPizza::MEDIA->value,
                                    quantidade: 1,
                                    saboresEscolhidos: [
                                        new SaborEscolhidoParaCadastro(
                                            idPizza: $idInexistente,
                                            ingredientesEscolhidos: [],
                                        ),
                                    ],
                                ),
                            ],
                        ),
                    );
                });

                expect($e)->toBeAnInstanceOf(DominioException::class);
                expect($e->erros())->toBe([
                    "Pizza (ID {$idInexistente}) não encontrada.",
                    'A quantidade de sabores deve ser entre 1 e 2.',
                ]);
            });

        });

        describe('IngredienteEscolhido', function () {

            it('lança exceção quando o ingrediente não existe', function () {
                allow($this->repositorioPizza)->toReceive('obterPizzaPorId')->andReturn($this->pizza);
                allow($this->repositorioIngrediente)->toReceive('obterIngredientePorId')->andReturn(null);

                $idInexistente = 999;

                /** @var DominioException $e */
                $e = capturarExcecao(function () use ($idInexistente) {
                    $this->gestorPedido->registrarPedido(
                        new PedidoParaCadastro(
                            tipoAtendimento: TipoAtendimento::BALCAO->value,
                            idCliente: 1,
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
                });

                expect($e)->toBeAnInstanceOf(DominioException::class);
                expect($e->erros())->toBe(["Ingrediente (ID {$idInexistente}) não encontrado."]);
            });

            it('lança exceção quando o papel do ingrediente é inválido', function () {
                allow($this->repositorioPizza)->toReceive('obterPizzaPorId')->andReturn($this->pizza);
                allow($this->repositorioIngrediente)->toReceive('obterIngredientePorId')->andReturn($this->queijo);

                $papelQueNaoExiste = 'papel_que_nao_existe';

                /** @var DominioException $e */
                $e = capturarExcecao(function () use ($papelQueNaoExiste) {
                    $this->gestorPedido->registrarPedido(
                        new PedidoParaCadastro(
                            tipoAtendimento: TipoAtendimento::BALCAO->value,
                            idCliente: 1,
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
                });

                expect($e)->toBeAnInstanceOf(DominioException::class);
                expect($e->erros())->toBe(["Papel do ingrediente inválido: {$papelQueNaoExiste}."]);
            });

            it('lança exceção quando o adicional não é encontrado', function () {
                allow($this->repositorioPizza)->toReceive('obterPizzaPorId')->andReturn($this->pizza);
                allow($this->repositorioIngrediente)->toReceive('obterIngredientePorId')->andReturn($this->queijo);

                /** @var DominioException $e */
                $e = capturarExcecao(function () {
                    $this->gestorPedido->registrarPedido(
                        new PedidoParaCadastro(
                            tipoAtendimento: TipoAtendimento::BALCAO->value,
                            idCliente: 1,
                            itensPedido: [
                                new ItemPizzaParaCadastro(
                                    tamanho: TamanhoPizza::MEDIA->value,
                                    quantidade: 1,
                                    saboresEscolhidos: [
                                        new SaborEscolhidoParaCadastro(
                                            idPizza: 1,
                                            ingredientesEscolhidos: [
                                                new IngredienteEscolhidoParaCadastro(
                                                    idIngrediente: 2,
                                                    papel: PapelIngrediente::PADRAO->value,
                                                ),
                                                new IngredienteEscolhidoParaCadastro(
                                                    idIngrediente: 1,
                                                    papel: PapelIngrediente::ADICIONAL->value,
                                                ),
                                            ],
                                        ),
                                    ],
                                ),
                            ],
                        ),
                    );
                });

                expect($e)->toBeAnInstanceOf(DominioException::class);
                expect($e->erros())->toBe([
                    'Ingrediente adicional (ID 2) não encontrado para a pizza (ID 1).',
                    'O ingrediente Mussarela(2) não é permitido como padrão para a pizza.',
                    'A quantidade de sabores deve ser entre 1 e 2.',
                ]);
            });

        });

        describe('ItemSimples', function () {

            it('lança exceção quando o produto simples não existe', function () {
                allow($this->repositorioProdutoSimples)->toReceive('obterProdutoSimplesPorId')->andReturn(null);

                $idInexistente = 999;

                /** @var DominioException $e */
                $e = capturarExcecao(function () use ($idInexistente) {
                    $this->gestorPedido->registrarPedido(
                        new PedidoParaCadastro(
                            tipoAtendimento: TipoAtendimento::BALCAO->value,
                            idCliente: 1,
                            itensPedido: [
                                new ItemSimplesParaCadastro(
                                    idProduto: $idInexistente,
                                    quantidade: 1,
                                ),
                            ],
                        ),
                    );
                });

                expect($e)->toBeAnInstanceOf(DominioException::class);
                expect($e->erros())->toBe(["Produto simples (ID {$idInexistente}) não encontrado."]);
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
                        tipoAtendimento: TipoAtendimento::BALCAO->value,
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
                        tipoAtendimento: TipoAtendimento::BALCAO->value,
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
                            validade: new DateTime(),
                            utilizado: false,
                        ),
                    ),
                );
                allow($this->repositorioPromocao)->toReceive('obterPromocoes')->andReturn([]);
                allow($this->repositorioProdutoSimples)->toReceive('obterProdutoSimplesPorId')->andReturn(null);
                allow($this->repositorioPizza)->toReceive('obterPizzaPorId')->andReturn($this->pizza);

                $pedido = $this->gestorPedido->registrarPedido(
                    new PedidoParaCadastro(
                        tipoAtendimento: TipoAtendimento::BALCAO->value,
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
                        tipoAtendimento: TipoAtendimento::BALCAO->value,
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

            it('cria pedido sem desconto de fidelidade quando o cliente tem beneficio inválido (valida expirada)', function () {
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
                        tipoAtendimento: TipoAtendimento::BALCAO->value,
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

            it('cria pedido sem desconto de fidelidade quando o cliente tem beneficio inválido (já utilizado)', function () {
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
                            validade: new DateTime(),
                            utilizado: true,
                        ),
                    ),
                );
                allow($this->repositorioPromocao)->toReceive('obterPromocoes')->andReturn([]);
                allow($this->repositorioProdutoSimples)->toReceive('obterProdutoSimplesPorId')->andReturn(null);
                allow($this->repositorioPizza)->toReceive('obterPizzaPorId')->andReturn($this->pizza);

                $pedido = $this->gestorPedido->registrarPedido(
                    new PedidoParaCadastro(
                        tipoAtendimento: TipoAtendimento::BALCAO->value,
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
                        tipoAtendimento: TipoAtendimento::BALCAO->value,
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
                        id: 1,
                        nome: 'João da Silva',
                        telefone: $this->telefone,
                        cpf: $this->cpf,
                        endereco: $this->endereco,
                        beneficioFidelidade: null,
                    ),
                );
                allow($this->repositorioPromocao)->toReceive('obterPromocoes')->andReturn([]);
                allow($this->repositorioProdutoSimples)->toReceive('obterProdutoSimplesPorId')->andReturn(ProdutoSimplesFactory::cocaCola(20));
                allow($this->repositorioPizza)->toReceive('obterPizzaPorId')->andReturn($this->pizza);

                $pedido = $this->gestorPedido->registrarPedido(
                    new PedidoParaCadastro(
                        tipoAtendimento: TipoAtendimento::BALCAO->value,
                        idCliente: 1,
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
                expect($pedido->cliente->id)->toBe(1);
                expect($pedido->itensPedido)->toHaveLength(2);
                expect($pedido->itensPedido[0]->tamanhoPizza)->toBe(TamanhoPizza::MEDIA);
                expect($pedido->itensPedido[1]->produtoSimples->id)->toBe(1);
                expect($pedido->itensPedido[1]->quantidade)->toBe(2);
            });

        });
    });

    describe('cancelarPedido()', function () {

        it('lança exceção quando o motivo do cancelamento não é informado', function () {
            /** @var DominioException $e */
            $e = capturarExcecao(function () {
                $this->gestorPedido->cancelarPedido(new CancelamentoParaCadastro(idPedido: 1, motivo: ''));
            });

            expect($e)->toBeAnInstanceOf(DominioException::class);
            expect($e->erros())->toBe(['O motivo do cancelamento é obrigatório.']);
        });

        it('altera o status para cancelado e registra o cancelamento na mesma transação', function () {
            allow($this->unidadeTrabalho)->toReceive('executar')->andRun(function (callable $operacao) {
                $operacao();
            });

            expect($this->repositorioPedido)->toReceive('alterarStatus')->with(1, StatusPedido::CANCELADO);
            expect($this->repositorioPedido)->toReceive('registrarCancelamento');

            $this->gestorPedido->cancelarPedido(new CancelamentoParaCadastro(idPedido: 1, motivo: 'Cliente desistiu do pedido'));
        });

    });

});

describe('GestorPedido - integração com banco de dados', function () {

    beforeAll(function () {
        $this->bancoDados = new BancoDados(PDOFactory::criar(
            nomeBanco: $_ENV['DB_NAME_TEST'],
            host: $_ENV['DB_HOST_TEST'],
            usuario: $_ENV['DB_USERNAME_TEST'],
            senha: $_ENV['DB_PASSWORD_TEST'],
        ));

        $repositorioCliente = new RepositorioClienteEmBDR($this->bancoDados);
        $repositorioPizza = new RepositorioPizzaEmBDR($this->bancoDados);
        $repositorioUsuario = new RepositorioUsuarioEmBDR();
        $repositorioPedido = new RepositorioPedidoEmBDR(
            bancoDados: $this->bancoDados,
            repositorioPizza: $repositorioPizza,
            repositorioCliente: $repositorioCliente,
            repositorioUsuario: $repositorioUsuario,
        );

        $this->gestorPedido = new GestorPedido(
            unidadeTrabalho: new UnidadeTrabalhoEmBDR($this->bancoDados),
            repositorioPedido: $repositorioPedido,
            repositorioCliente: $repositorioCliente,
            repositorioPizza: $repositorioPizza,
            repositorioProdutoSimples: new RepositorioProdutoSimplesEmBDR($this->bancoDados),
            repositorioIngrediente: new RepositorioIngredienteEmBDR($this->bancoDados),
            repositorioPromocao: new RepositorioPromocaoEmBDR($this->bancoDados),
            repositorioConfiguracaoFidelidade: new RepositorioConfiguracaoFidelidadeEmBDR($this->bancoDados),
            repositorioMovimentacaoEstoque: new RepositorioMovimentacaoEstoqueEmBDR($this->bancoDados),
        );
    });

    describe('registrarPedido()', function () {

        it('marca o benefício como utilizado quando o pedido tem desconto fidelidade', function () {
            $beneficioAntes = $this->bancoDados->consultar(
                'SELECT id, utilizado FROM beneficio_fidelidade WHERE idCliente = :idCliente ORDER BY id',
                ['idCliente' => 11],
            )[0] ?? null;

            $this->gestorPedido->registrarPedido(new PedidoParaCadastro(
                tipoAtendimento: TipoAtendimento::BALCAO->value,
                idCliente: '11',
                itensPedido: [
                    new ItemSimplesParaCadastro(
                        idProduto: 1,
                        quantidade: 10,
                    ),
                ],
            ));

            $beneficioDepois = $this->bancoDados->consultar(
                'SELECT id, utilizado FROM beneficio_fidelidade
                    WHERE id = :id
                    ORDER BY id',
                ['id' => $beneficioAntes['id']],
            )[0] ?? null;

            expect($beneficioDepois)->toBe([
                'id' => $beneficioAntes['id'],
                'utilizado' => 1,
            ]);
        });

        it('gera um novo benefício de fidelidade para o cliente quando o pedido atinge as regras', function () {
            $quantidadeAntes = $this->bancoDados->consultar(
                'SELECT COUNT(*) AS total FROM beneficio_fidelidade WHERE idCliente = :idCliente',
                ['idCliente' => 11],
            )[0]['total'] ?? 0;

            $pedido = $this->gestorPedido->registrarPedido(new PedidoParaCadastro(
                tipoAtendimento: TipoAtendimento::BALCAO->value,
                idCliente: '11',
                itensPedido: [
                    new ItemSimplesParaCadastro(
                        idProduto: 1,
                        quantidade: 10,
                    ),
                    new ItemPizzaParaCadastro(
                        tamanho: TamanhoPizza::MEDIA->value,
                        quantidade: 4,
                        saboresEscolhidos: [
                            new SaborEscolhidoParaCadastro(
                                idPizza: 1,
                                ingredientesEscolhidos: [
                                    new IngredienteEscolhidoParaCadastro(
                                        idIngrediente: 1,
                                        papel: PapelIngrediente::PADRAO->value,
                                    ),
                                ],
                            ),
                        ],
                    ),
                ],
            ));

            $beneficiosDepois = $this->bancoDados->consultar(
                'SELECT id, percentualDesconto, dataConcessao, validade, utilizado
                    FROM beneficio_fidelidade
                    WHERE idCliente = :idCliente
                    ORDER BY id DESC',
                ['idCliente' => 11],
            );

            $beneficioDepois = $beneficiosDepois[0] ?? null;
            $novaQuantidade = $quantidadeAntes + 1;
            $quantidadeDepois = count($beneficiosDepois);

            expect($quantidadeDepois)->toBe($novaQuantidade);
            expect($beneficioDepois)->toBe([
                'id' => $beneficioDepois['id'],
                'percentualDesconto' => $beneficioDepois['percentualDesconto'],
                'dataConcessao' => $beneficioDepois['dataConcessao'],
                'validade' => $beneficioDepois['validade'],
                'utilizado' => 0,
            ]);
        });

        it('não gera um novo benefício de fidelidade para o cliente quando o pedido não atinge as regras', function () {
            $quantidadeAntes = $this->bancoDados->consultar(
                'SELECT COUNT(*) AS total FROM beneficio_fidelidade WHERE idCliente = :idCliente',
                ['idCliente' => 11],
            )[0]['total'] ?? 0;

            $pedido = $this->gestorPedido->registrarPedido(new PedidoParaCadastro(
                tipoAtendimento: TipoAtendimento::BALCAO->value,
                idCliente: '11',
                itensPedido: [
                    new ItemSimplesParaCadastro(
                        idProduto: 1,
                        quantidade: 1,
                    ),
                ],
            ));

            $quantidadeDepois = $this->bancoDados->consultar(
                'SELECT COUNT(*) AS total FROM beneficio_fidelidade WHERE idCliente = :idCliente',
                ['idCliente' => 11],
            )[0]['total'] ?? 0;

            expect($quantidadeDepois)->toBe($quantidadeAntes);
        });

    });

});
