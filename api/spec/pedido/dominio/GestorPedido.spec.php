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

function compararMovimentacaoEstoqueIngrediente(
    MovimentacaoEstoqueIngrediente $movimentacao,
    int $idIngredienteEsperado,
    int $valorEsperado,
    int $idUnidadeMedidaEsperado,
    TipoOperacaoEstoque $tipoOperacaoEstoqueEsperado,
) {
    expect($movimentacao->ingrediente->id)->toBe($idIngredienteEsperado);
    expect($movimentacao->quantidadeMedida->valor)->toBe($valorEsperado);
    expect($movimentacao->quantidadeMedida->unidadeMedida->id)->toBe($idUnidadeMedidaEsperado);
    expect($movimentacao->tipoOperacaoEstoque)->toBe($tipoOperacaoEstoqueEsperado);
}

function compararMovimentacaoEstoqueProdutoSimples(
    MovimentacaoEstoqueProdutoSimples $movimentacao,
    int $idProdutoSimplesEsperado,
    int $valorEsperado,
    int $idUnidadeMedidaEsperado,
    TipoOperacaoEstoque $tipoOperacaoEstoqueEsperado,
) {
    expect($movimentacao->produtoSimples->id)->toBe($idProdutoSimplesEsperado);
    expect($movimentacao->quantidadeMedida->valor)->toBe($valorEsperado);
    expect($movimentacao->quantidadeMedida->unidadeMedida->id)->toBe($idUnidadeMedidaEsperado);
    expect($movimentacao->tipoOperacaoEstoque)->toBe($tipoOperacaoEstoqueEsperado);
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
        $this->repositorioEntregador = Double::instance(['implements' => RepositorioEntregador::class]);
        $this->provedorUsuarioLogado = Double::instance(['implements' => ProvedorUsuarioLogado::class]);
        allow($this->provedorUsuarioLogado)->toReceive('obterUsuarioLogado')->andReturn(UsuarioFactory::atendente());

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
            repositorioEntregador: $this->repositorioEntregador,
            provedorUsuarioLogado: $this->provedorUsuarioLogado,
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

    describe('alterarStatus()', function () {

        it('lança exceção quando o motivo do cancelamento não é informado', function () {
            /** @var DominioException $e */
            $e = capturarExcecao(function () {
                $this->gestorPedido->alterarStatus(1, StatusPedido::CANCELADO->value, '');
            });

            expect($e)->toBeAnInstanceOf(DominioException::class);
            expect($e->erros())->toBe(['O motivo do cancelamento é obrigatório.']);
        });

    });

    describe('obterDetalhesPedido()', function () {

        it('retorna os detalhes do pedido com os ingredientes removidos, substitutos e adicionais de cada sabor', function () {
            allow($this->repositorioPedido)->toReceive('obterPedidoPorId')->andReturn(new Pedido(
                id: 1,
                usuario: UsuarioFactory::atendente(),
                data: new DateTime(),
                tipoAtendimento: TipoAtendimento::BALCAO,
                cliente: null,
                itensPedido: [
                    new ItemPizza(
                        id: 1,
                        quantidade: 1,
                        saboresEscolhidos: [
                            new SaborEscolhido(
                                id: 1,
                                pizza: PizzaFactory::portuguesa(),
                                ingredientesEscolhidos: [
                                    IngredienteEscolhidoFactory::padrao(1, IngredienteFactory::molho()),
                                    IngredienteEscolhidoFactory::padrao(2, IngredienteFactory::mussarela()),
                                    IngredienteEscolhidoFactory::padrao(3, IngredienteFactory::presunto()),
                                    IngredienteEscolhidoFactory::substituto(4, IngredienteFactory::cebola()),
                                    IngredienteEscolhidoFactory::adicional(5, IngredienteFactory::bacon(), 899),
                                    IngredienteEscolhidoFactory::adicional(6, IngredienteFactory::bacon(), 899),
                                ],
                            ),
                        ],
                        tamanhoPizza: TamanhoPizza::MEDIA,
                    ),
                ],
                status: StatusPedido::RECEBIDO,
            ));

            $detalhesPedido = $this->gestorPedido->obterDetalhesPedido(1);

            $detalhesSabor = $detalhesPedido->itens[0]->sabores[0];
            expect($detalhesSabor->nome)->toBe('Portuguesa');
            expect($detalhesSabor->ingredientesRemovidos)->toBe([IngredienteFactory::azeitona()->nome]);
            expect($detalhesSabor->ingredientesSubstitutos)->toBe([IngredienteFactory::cebola()->nome]);
            expect($detalhesSabor->adicionais)->toEqual([new DetalhesAdicionalSabor(nome: IngredienteFactory::bacon()->nome, quantidade: 2)]);
        });

    });

    describe('registrarSaidaParaEntrega()', function () {

        beforeEach(function () {
            allow($this->repositorioPedido)->toReceive('obterPedidoPorId')->andReturn(new Pedido(
                id: 1,
                usuario: UsuarioFactory::atendente(),
                data: new DateTime(),
                tipoAtendimento: TipoAtendimento::ENTREGA,
                cliente: new Cliente(
                    id: 1,
                    nome: 'João da Silva',
                    telefone: $this->telefone,
                    cpf: $this->cpf,
                    endereco: $this->endereco,
                    beneficioFidelidade: null,
                ),
                itensPedido: [
                    new ItemSimples(id: 1, quantidade: 1, produtoSimples: ProdutoSimplesFactory::cocaCola(20)),
                ],
                status: StatusPedido::EM_PREPARACAO,
            ));
        });

        it('lança exceção quando o entregador não está disponível', function () {
            $entregador = EntregadorFactory::entregador(disponivel: false);
            allow($this->repositorioEntregador)->toReceive('obterEntregadorPorId')->andReturn($entregador);

            /** @var DominioException $e */
            $e = capturarExcecao(function () use ($entregador) {
                $this->gestorPedido->registrarSaidaParaEntrega(new SaidaParaEntregaParaCadastro(idPedido: 1, idEntregador: $entregador->id));
            });

            expect($e)->toBeAnInstanceOf(DominioException::class);
            expect($e->erros())->toBe(["O entregador {$entregador->nome} (ID {$entregador->id}) não está disponível."]);
        });

        it('registra a entrega com o entregador, o endereço do cliente e a data de saída', function () {
            $entregador = EntregadorFactory::entregador();
            allow($this->repositorioEntregador)->toReceive('obterEntregadorPorId')->andReturn($entregador);
            allow($this->unidadeTrabalho)->toReceive('executar')->andRun(function (callable $operacao) {
                $operacao();
            });

            $entregaRegistrada = null;
            allow($this->repositorioPedido)->toReceive('registrarEntrega')->andRun(function (Pedido $pedido, Entrega $entrega) use (&$entregaRegistrada) {
                $entregaRegistrada = $entrega;
            });

            $this->gestorPedido->registrarSaidaParaEntrega(new SaidaParaEntregaParaCadastro(idPedido: 1, idEntregador: $entregador->id));

            expect($entregaRegistrada)->toBeAnInstanceOf(Entrega::class);
            expect($entregaRegistrada->entregador)->toBe($entregador);
            expect($entregaRegistrada->endereco)->toBe($this->endereco);
            expect($entregaRegistrada->dataSaida)->toBeAnInstanceOf(DateTime::class);
            expect($entregaRegistrada->dataChegada)->toBeNull();
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

        $this->gestorPedido = GestorFactory::criarGestorPedido(
            bancoDados: $this->bancoDados,
            provedorUsuarioLogado: new UsuarioLogadoFixo(UsuarioFactory::atendente()),
        );

        $this->repositorioCliente = new RepositorioClienteEmBDR($this->bancoDados);
        $this->repositorioProdutoSimples = new RepositorioProdutoSimplesEmBDR($this->bancoDados);
        $this->repositorioIngrediente = new RepositorioIngredienteEmBDR($this->bancoDados);
        $this->repositorioPedido = new RepositorioPedidoEmBDR(
            bancoDados: $this->bancoDados,
            repositorioPizza: new RepositorioPizzaEmBDR($this->bancoDados),
            repositorioCliente: $this->repositorioCliente,
            repositorioUsuario: new RepositorioUsuarioEmBDR($this->bancoDados),
            repositorioEntregador: new RepositorioEntregadorEmBDR($this->bancoDados),
        );

        $this->idCocaCola = 1;
        $this->idMolhoTomate = 1;
        $this->idMussarela = 2;
        $this->idCalabresa = 4;
        $this->idPizzaMargherita = 1;
        $this->idPizzaCalabresa = 2;

        $this->pedidoMeiaPizza = new PedidoParaCadastro(
            tipoAtendimento: TipoAtendimento::BALCAO->value,
            idCliente: '',
            itensPedido: [
                new ItemSimplesParaCadastro(
                    idProduto: $this->idCocaCola,
                    quantidade: 1,
                ),
                new ItemPizzaParaCadastro(
                    tamanho: TamanhoPizza::PEQUENA->value,
                    quantidade: 2,
                    saboresEscolhidos: [
                        new SaborEscolhidoParaCadastro(
                            idPizza: $this->idPizzaMargherita,
                            ingredientesEscolhidos: [
                                new IngredienteEscolhidoParaCadastro(
                                    idIngrediente: $this->idMolhoTomate,
                                    papel: PapelIngrediente::PADRAO->value,
                                ),
                                new IngredienteEscolhidoParaCadastro(
                                    idIngrediente: $this->idMussarela,
                                    papel: PapelIngrediente::PADRAO->value,
                                ),
                            ],
                        ),
                        new SaborEscolhidoParaCadastro(
                            idPizza: $this->idPizzaCalabresa,
                            ingredientesEscolhidos: [
                                new IngredienteEscolhidoParaCadastro(
                                    idIngrediente: $this->idMolhoTomate,
                                    papel: PapelIngrediente::PADRAO->value,
                                ),
                                new IngredienteEscolhidoParaCadastro(
                                    idIngrediente: $this->idMussarela,
                                    papel: PapelIngrediente::PADRAO->value,
                                ),
                                new IngredienteEscolhidoParaCadastro(
                                    idIngrediente: $this->idCalabresa,
                                    papel: PapelIngrediente::PADRAO->value,
                                ),
                            ],
                        ),
                    ],
                ),
            ],
        );
    });

    describe('registrarPedido()', function () {

        describe('marcarBeneficioFidelidadeComoUtilizado()', function () {

            beforeAll(function () {
                $this->idCliente = 11; // Artur Alves
            });

            it('marca o benefício como utilizado quando o pedido tem desconto fidelidade', function () {
                $clienteAntes = $this->repositorioCliente->obterClientePorId($this->idCliente);
                $beneficioAntes = $clienteAntes->beneficioFidelidade;

                $this->gestorPedido->registrarPedido(new PedidoParaCadastro(
                    tipoAtendimento: TipoAtendimento::BALCAO->value,
                    idCliente: $this->idCliente,
                    itensPedido: [
                        new ItemSimplesParaCadastro(
                            idProduto: 1,
                            quantidade: 1,
                        ),
                    ],
                ));

                $clienteDepois = $this->repositorioCliente->obterClientePorId($this->idCliente);
                $beneficioDepois = $this->repositorioCliente->obterBeneficioFidelidadePorId($beneficioAntes->id);

                expect($beneficioAntes)->toBeAnInstanceOf(BeneficioFidelidade::class);
                expect($beneficioAntes->utilizado)->toBe(false);

                expect($beneficioDepois)->toBeAnInstanceOf(BeneficioFidelidade::class);
                expect($beneficioDepois->id)->toBe($beneficioAntes->id);
                expect($beneficioDepois->utilizado)->toBe(true);

                expect($clienteDepois->beneficioFidelidade)->toBe(null);
            });

            it('não marca o benefício como utilizado quando o pedido não tem desconto fidelidade', function () {
                $clienteAntes = $this->repositorioCliente->obterClientePorId($this->idCliente);
                $beneficioAntes = $clienteAntes->beneficioFidelidade;

                $this->gestorPedido->registrarPedido(new PedidoParaCadastro(
                    tipoAtendimento: TipoAtendimento::BALCAO->value,
                    idCliente: $this->idCliente,
                    itensPedido: [
                        new ItemSimplesParaCadastro(
                            idProduto: 2,
                            quantidade: 1,
                        ),
                    ],
                ));

                $clienteDepois = $this->repositorioCliente->obterClientePorId($this->idCliente);
                $beneficioDepois = $clienteDepois->beneficioFidelidade;

                expect($beneficioAntes)->toBe(null);
                expect($beneficioDepois)->toBe(null);
            });

        });

        describe('gerarNovoBeneficioFidelidade()', function () {

            it('gera um novo benefício de fidelidade para o cliente quando o pedido atinge as regras', function () {
                $idCliente = 2; // Carlos Eduardo Lima

                $clienteAntes = $this->repositorioCliente->obterClientePorId($idCliente);
                $beneficioAntes = $clienteAntes->beneficioFidelidade;

                $this->gestorPedido->registrarPedido(new PedidoParaCadastro(
                    tipoAtendimento: TipoAtendimento::BALCAO->value,
                    idCliente: $idCliente,
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

                $clienteDepois = $this->repositorioCliente->obterClientePorId($idCliente);
                $beneficioDepois = $clienteDepois->beneficioFidelidade;

                expect($beneficioAntes)->toBe(null);

                expect($beneficioDepois)->toBeAnInstanceOf(BeneficioFidelidade::class);
                expect($beneficioDepois->percentualDesconto->valor)->toBe(5);
                expect($beneficioDepois->dataConcessao)->toBeAnInstanceOf(DateTime::class);
                expect($beneficioDepois->validade)->toBeAnInstanceOf(DateTime::class);
                expect($beneficioDepois->utilizado)->toBe(false);
            });

            it('não gera benefício de fidelidade para o cliente quando o pedido não atinge as regras', function () {
                $idCliente = 3; // Fernanda Oliveira Costa

                $clienteAntes = $this->repositorioCliente->obterClientePorId($idCliente);
                $beneficioAntes = $clienteAntes->beneficioFidelidade;

                $this->gestorPedido->registrarPedido(new PedidoParaCadastro(
                    tipoAtendimento: TipoAtendimento::BALCAO->value,
                    idCliente: '11',
                    itensPedido: [
                        new ItemSimplesParaCadastro(
                            idProduto: 1,
                            quantidade: 1,
                        ),
                    ],
                ));

                $clienteDepois = $this->repositorioCliente->obterClientePorId($idCliente);
                $beneficioDepois = $clienteDepois->beneficioFidelidade;

                expect($beneficioAntes)->toBe(null);
                expect($beneficioDepois)->toBe(null);
            });

        });

        describe('atualizarEstoqueAposRegistrarPedido()', function () {

            it('atualiza o estoque dos produtos simples e ingredientes após registrar o pedido', function () {
                $cocaColaAntes = $this->repositorioProdutoSimples->obterProdutoSimplesPorId($this->idCocaCola);
                $molhoTomateAntes = $this->repositorioIngrediente->obterIngredientePorId($this->idMolhoTomate);
                $mussarelaAntes = $this->repositorioIngrediente->obterIngredientePorId($this->idMussarela);

                $pedido = $this->gestorPedido->registrarPedido(new PedidoParaCadastro(
                    tipoAtendimento: TipoAtendimento::BALCAO->value,
                    idCliente: '',
                    itensPedido: [
                        new ItemSimplesParaCadastro(
                            idProduto: $this->idCocaCola,
                            quantidade: 2,
                        ),
                        new ItemPizzaParaCadastro(
                            tamanho: TamanhoPizza::MEDIA->value,
                            quantidade: 2,
                            saboresEscolhidos: [
                                new SaborEscolhidoParaCadastro(
                                    idPizza: $this->idPizzaMargherita,
                                    ingredientesEscolhidos: [
                                        new IngredienteEscolhidoParaCadastro(
                                            idIngrediente: $this->idMolhoTomate,
                                            papel: PapelIngrediente::PADRAO->value,
                                        ),
                                        new IngredienteEscolhidoParaCadastro(
                                            idIngrediente: $this->idMussarela,
                                            papel: PapelIngrediente::PADRAO->value,
                                        ),
                                    ],
                                ),
                            ],
                        ),
                    ],
                ));

                $cocaColaDepois = $this->repositorioProdutoSimples->obterProdutoSimplesPorId($this->idCocaCola);
                $molhoTomateDepois = $this->repositorioIngrediente->obterIngredientePorId($this->idMolhoTomate);
                $mussarelaDepois = $this->repositorioIngrediente->obterIngredientePorId($this->idMussarela);

                /** @var Pedido */
                $pedidoAtualizado = $this->repositorioPedido->obterPedidoPorId($pedido->id);
                $movimentacoesEstoqueSaida = $pedidoAtualizado->obterMovimentacoesEstoque(TipoOperacaoEstoque::SAIDA);
                [$movimentacaoEstoqueMolho, $movimentacaoEstoqueMussarela, $movimentacaoEstoqueCocaCola] = $movimentacoesEstoqueSaida;

                expect($cocaColaDepois->estoque->valor)->toBe($cocaColaAntes->estoque->valor - 2);
                expect($molhoTomateDepois->estoque->valor)->toBe($molhoTomateAntes->estoque->valor - 150);
                expect($mussarelaDepois->estoque->valor)->toBe($mussarelaAntes->estoque->valor - 300);
                expect($movimentacoesEstoqueSaida)->toHaveLength(3);

                compararMovimentacaoEstoqueIngrediente(
                    movimentacao: $movimentacaoEstoqueMolho,
                    idIngredienteEsperado: $this->idMolhoTomate,
                    valorEsperado: 150,
                    idUnidadeMedidaEsperado: 3,
                    tipoOperacaoEstoqueEsperado: TipoOperacaoEstoque::SAIDA,
                );

                compararMovimentacaoEstoqueIngrediente(
                    movimentacao: $movimentacaoEstoqueMussarela,
                    idIngredienteEsperado: $this->idMussarela,
                    valorEsperado: 300,
                    idUnidadeMedidaEsperado: 1,
                    tipoOperacaoEstoqueEsperado: TipoOperacaoEstoque::SAIDA,
                );

                compararMovimentacaoEstoqueProdutoSimples(
                    movimentacao: $movimentacaoEstoqueCocaCola,
                    idProdutoSimplesEsperado: $this->idCocaCola,
                    valorEsperado: 2,
                    idUnidadeMedidaEsperado: 2,
                    tipoOperacaoEstoqueEsperado: TipoOperacaoEstoque::SAIDA,
                );
            });

            it('atualiza o estoque dos produtos simples e ingredientes após registrar o pedido (meia pizza)', function () {
                $cocaColaAntes = $this->repositorioProdutoSimples->obterProdutoSimplesPorId($this->idCocaCola);
                $molhoTomateAntes = $this->repositorioIngrediente->obterIngredientePorId($this->idMolhoTomate);
                $mussarelaAntes = $this->repositorioIngrediente->obterIngredientePorId($this->idMussarela);
                $calabresaAntes = $this->repositorioIngrediente->obterIngredientePorId($this->idCalabresa);

                $pedido = $this->gestorPedido->registrarPedido($this->pedidoMeiaPizza);

                $cocaColaDepois = $this->repositorioProdutoSimples->obterProdutoSimplesPorId($this->idCocaCola);
                $molhoTomateDepois = $this->repositorioIngrediente->obterIngredientePorId($this->idMolhoTomate);
                $mussarelaDepois = $this->repositorioIngrediente->obterIngredientePorId($this->idMussarela);
                $calabresaDepois = $this->repositorioIngrediente->obterIngredientePorId($this->idCalabresa);

                /** @var Pedido */
                $pedidoAtualizado = $this->repositorioPedido->obterPedidoPorId($pedido->id);
                $movimentacoesEstoqueSaida = $pedidoAtualizado->obterMovimentacoesEstoque(TipoOperacaoEstoque::SAIDA);
                [
                    $movimentacaoEstoqueMolho1,
                    $movimentacaoEstoqueMolho2,
                    $movimentacaoEstoqueMussarela1,
                    $movimentacaoEstoqueMussarela2,
                    $movimentacaoEstoqueCalabresa,
                    $movimentacaoEstoqueCocaCola,
                ] = $movimentacoesEstoqueSaida;

                expect($molhoTomateDepois->estoque->valor)->toBe($molhoTomateAntes->estoque->valor - 100);
                expect($mussarelaDepois->estoque->valor)->toBe($mussarelaAntes->estoque->valor - 200);
                expect($calabresaDepois->estoque->valor)->toBe($calabresaAntes->estoque->valor - 100);
                expect($cocaColaDepois->estoque->valor)->toBe($cocaColaAntes->estoque->valor - 1);
                expect($movimentacoesEstoqueSaida)->toHaveLength(6);

                compararMovimentacaoEstoqueIngrediente(
                    movimentacao: $movimentacaoEstoqueMolho1,
                    idIngredienteEsperado: $this->idMolhoTomate,
                    valorEsperado: 50,
                    idUnidadeMedidaEsperado: 3,
                    tipoOperacaoEstoqueEsperado: TipoOperacaoEstoque::SAIDA,
                );

                compararMovimentacaoEstoqueIngrediente(
                    movimentacao: $movimentacaoEstoqueMussarela1,
                    idIngredienteEsperado: $this->idMussarela,
                    valorEsperado: 100,
                    idUnidadeMedidaEsperado: 1,
                    tipoOperacaoEstoqueEsperado: TipoOperacaoEstoque::SAIDA,
                );

                compararMovimentacaoEstoqueIngrediente(
                    movimentacao: $movimentacaoEstoqueMolho2,
                    idIngredienteEsperado: $this->idMolhoTomate,
                    valorEsperado: 50,
                    idUnidadeMedidaEsperado: 3,
                    tipoOperacaoEstoqueEsperado: TipoOperacaoEstoque::SAIDA,
                );

                compararMovimentacaoEstoqueIngrediente(
                    movimentacao: $movimentacaoEstoqueMussarela2,
                    idIngredienteEsperado: $this->idMussarela,
                    valorEsperado: 100,
                    idUnidadeMedidaEsperado: 1,
                    tipoOperacaoEstoqueEsperado: TipoOperacaoEstoque::SAIDA,
                );

                compararMovimentacaoEstoqueIngrediente(
                    movimentacao: $movimentacaoEstoqueCalabresa,
                    idIngredienteEsperado: $this->idCalabresa,
                    valorEsperado: 100,
                    idUnidadeMedidaEsperado: 1,
                    tipoOperacaoEstoqueEsperado: TipoOperacaoEstoque::SAIDA,
                );

                compararMovimentacaoEstoqueProdutoSimples(
                    movimentacao: $movimentacaoEstoqueCocaCola,
                    idProdutoSimplesEsperado: $this->idCocaCola,
                    valorEsperado: 1,
                    idUnidadeMedidaEsperado: 2,
                    tipoOperacaoEstoqueEsperado: TipoOperacaoEstoque::SAIDA,
                );
            });

        });

    });

    describe('alterarStatus()', function () {

        describe('cancelarPedido()', function () {

            describe('atualizarEstoqueAposCancelarPedido()', function () {

                it('atualiza o estoque dos produtos simples e ingredientes após cancelar o pedido', function () {
                    $pedido = $this->gestorPedido->registrarPedido($this->pedidoMeiaPizza);

                    $cocaColaAntes = $this->repositorioProdutoSimples->obterProdutoSimplesPorId($this->idCocaCola);
                    $molhoTomateAntes = $this->repositorioIngrediente->obterIngredientePorId($this->idMolhoTomate);
                    $mussarelaAntes = $this->repositorioIngrediente->obterIngredientePorId($this->idMussarela);
                    $calabresaAntes = $this->repositorioIngrediente->obterIngredientePorId($this->idCalabresa);

                    $this->gestorPedido->alterarStatus($pedido->id, StatusPedido::CANCELADO->value, 'Motivo do cancelamento');

                    $cocaColaDepois = $this->repositorioProdutoSimples->obterProdutoSimplesPorId($this->idCocaCola);
                    $molhoTomateDepois = $this->repositorioIngrediente->obterIngredientePorId($this->idMolhoTomate);
                    $mussarelaDepois = $this->repositorioIngrediente->obterIngredientePorId($this->idMussarela);
                    $calabresaDepois = $this->repositorioIngrediente->obterIngredientePorId($this->idCalabresa);

                    /** @var Pedido */
                    $pedidoAtualizado = $this->repositorioPedido->obterPedidoPorId($pedido->id);
                    $movimentacoesEstoqueEntrada = $pedidoAtualizado->obterMovimentacoesEstoque(TipoOperacaoEstoque::ENTRADA);
                    [
                        $movimentacaoEstoqueMolho1,
                        $movimentacaoEstoqueMolho2,
                        $movimentacaoEstoqueMussarela1,
                        $movimentacaoEstoqueMussarela2,
                        $movimentacaoEstoqueCalabresa,
                        $movimentacaoEstoqueCocaCola,
                    ] = $movimentacoesEstoqueEntrada;

                    expect($molhoTomateDepois->estoque->valor)->toBe($molhoTomateAntes->estoque->valor + 100);
                    expect($mussarelaDepois->estoque->valor)->toBe($mussarelaAntes->estoque->valor + 200);
                    expect($calabresaDepois->estoque->valor)->toBe($calabresaAntes->estoque->valor + 100);
                    expect($cocaColaDepois->estoque->valor)->toBe($cocaColaAntes->estoque->valor + 1);
                    expect($movimentacoesEstoqueEntrada)->toHaveLength(6);

                    compararMovimentacaoEstoqueIngrediente(
                        movimentacao: $movimentacaoEstoqueMolho1,
                        idIngredienteEsperado: $this->idMolhoTomate,
                        valorEsperado: 50,
                        idUnidadeMedidaEsperado: 3,
                        tipoOperacaoEstoqueEsperado: TipoOperacaoEstoque::ENTRADA,
                    );

                    compararMovimentacaoEstoqueIngrediente(
                        movimentacao: $movimentacaoEstoqueMussarela1,
                        idIngredienteEsperado: $this->idMussarela,
                        valorEsperado: 100,
                        idUnidadeMedidaEsperado: 1,
                        tipoOperacaoEstoqueEsperado: TipoOperacaoEstoque::ENTRADA,
                    );

                    compararMovimentacaoEstoqueIngrediente(
                        movimentacao: $movimentacaoEstoqueMolho2,
                        idIngredienteEsperado: $this->idMolhoTomate,
                        valorEsperado: 50,
                        idUnidadeMedidaEsperado: 3,
                        tipoOperacaoEstoqueEsperado: TipoOperacaoEstoque::ENTRADA,
                    );

                    compararMovimentacaoEstoqueIngrediente(
                        movimentacao: $movimentacaoEstoqueMussarela2,
                        idIngredienteEsperado: $this->idMussarela,
                        valorEsperado: 100,
                        idUnidadeMedidaEsperado: 1,
                        tipoOperacaoEstoqueEsperado: TipoOperacaoEstoque::ENTRADA,
                    );

                    compararMovimentacaoEstoqueIngrediente(
                        movimentacao: $movimentacaoEstoqueCalabresa,
                        idIngredienteEsperado: $this->idCalabresa,
                        valorEsperado: 100,
                        idUnidadeMedidaEsperado: 1,
                        tipoOperacaoEstoqueEsperado: TipoOperacaoEstoque::ENTRADA,
                    );

                    compararMovimentacaoEstoqueProdutoSimples(
                        movimentacao: $movimentacaoEstoqueCocaCola,
                        idProdutoSimplesEsperado: $this->idCocaCola,
                        valorEsperado: 1,
                        idUnidadeMedidaEsperado: 2,
                        tipoOperacaoEstoqueEsperado: TipoOperacaoEstoque::ENTRADA,
                    );
                });

            });

        });

    });

});
