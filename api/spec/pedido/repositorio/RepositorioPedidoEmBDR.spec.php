<?php

describe('RepositorioPedidoEmBDR', function () {

    beforeAll(function () {
        $this->bancoDados = new BancoDados(PDOFactory::criar(
            nomeBanco: $_ENV['DB_NAME_TEST'],
            host: $_ENV['DB_HOST_TEST'],
            usuario: $_ENV['DB_USERNAME_TEST'],
            senha: $_ENV['DB_PASSWORD_TEST'],
        ));

        $this->repositorioUsuario = new RepositorioUsuarioEmBDR($this->bancoDados);
        $this->repositorioCliente = new RepositorioClienteEmBDR($this->bancoDados);
        $this->repositorioPizza = new RepositorioPizzaEmBDR($this->bancoDados);
        $this->repositorioProdutoSimples = new RepositorioProdutoSimplesEmBDR($this->bancoDados);
        $this->repositorioPromocao = new RepositorioPromocaoEmBDR($this->bancoDados);
        $this->repositorioEntregador = new RepositorioEntregadorEmBDR($this->bancoDados);
        $this->repositorioPedido = new RepositorioPedidoEmBDR(
            bancoDados: $this->bancoDados,
            repositorioPizza: $this->repositorioPizza,
            repositorioCliente: $this->repositorioCliente,
            repositorioUsuario: $this->repositorioUsuario,
            repositorioEntregador: $this->repositorioEntregador,
        );
    });

    describe('registrarPedido()', function () {

        beforeAll(function () {
            $this->usuario = $this->repositorioUsuario->obterUsuarioPorId(1);
            $this->cliente = $this->repositorioCliente->obterClientePorId(10);
            $this->pizzaBacon = $this->repositorioPizza->obterPizzaPorId(10);
            $this->pizzaPortuguesa = $this->repositorioPizza->obterPizzaPorId(5);
            $this->pudim = $this->repositorioProdutoSimples->obterProdutoSimplesPorId(5);
            $this->promocao = $this->repositorioPromocao->obterPromocaoPorId(1);

            $this->pedido = new Pedido(
                id: 0,
                usuario: $this->usuario,
                data: new DateTime(),
                tipoAtendimento: TipoAtendimento::ENTREGA,
                cliente: $this->cliente,
                itensPedido: [
                    new ItemPizza(
                        id: 0,
                        quantidade: 2,
                        saboresEscolhidos: [
                            new SaborEscolhido(
                                id: 0,
                                pizza: $this->pizzaBacon,
                                ingredientesEscolhidos: array_map(function (IngredientePadrao $ingredientePadrao) {
                                    return IngredienteEscolhidoFactory::padrao(0, $ingredientePadrao->ingrediente);
                                }, $this->pizzaBacon->ingredientesPadrao),
                            ),
                            new SaborEscolhido(
                                id: 0,
                                pizza: $this->pizzaPortuguesa,
                                ingredientesEscolhidos: array_map(function (IngredientePadrao $ingredientePadrao) {
                                    return IngredienteEscolhidoFactory::padrao(0, $ingredientePadrao->ingrediente);
                                }, $this->pizzaPortuguesa->ingredientesPadrao),
                            ),
                        ],
                        tamanhoPizza: TamanhoPizza::GRANDE,
                    ),
                    new ItemSimples(
                        id: 0,
                        quantidade: 1,
                        produtoSimples: $this->pudim,
                    ),
                ],
                status: StatusPedido::RECEBIDO,
                descontosPromocao: [
                    new DescontoPromocao(
                        id: 0,
                        promocao: $this->promocao,
                        valorDesconto: new Dinheiro(centavos: 1150),
                    ),
                ],
                descontoFidelidade: new DescontoFidelidade(
                    id: 0,
                    valorDesconto: new Dinheiro(centavos: 1185),
                    beneficioFidelidade: $this->cliente->beneficioFidelidade,
                ),
            );

            $this->pedidoErro = new Pedido(
                id: 0,
                data: new DateTime('-1 minute'),
                usuario: UsuarioFactory::atendente(),
                tipoAtendimento: TipoAtendimento::BALCAO,
                cliente: null,
                itensPedido: [
                    new ItemSimples(
                        1,
                        1,
                        ProdutoSimplesFactory::inexistente(),
                    ),
                ],
                status: StatusPedido::RECEBIDO,
            );
        });

        it('registra o pedido e todas as suas relações', function () {
            $this->repositorioPedido->registrarPedido($this->pedido);
            $pedidoSalvo = $this->repositorioPedido->obterPedidoPorId($this->pedido->id);

            expect($pedidoSalvo)->toBeAnInstanceOf(Pedido::class);
            expect($pedidoSalvo->usuario)->toBeAnInstanceOf(Usuario::class);
            expect($pedidoSalvo->usuario->id)->toBe($this->pedido->usuario->id);
            expect($pedidoSalvo->data)->toBeAnInstanceOf(DateTime::class);
            expect($pedidoSalvo->tipoAtendimento)->toBe($this->pedido->tipoAtendimento);
            expect($pedidoSalvo->cliente)->toBeAnInstanceOf(Cliente::class);
            expect($pedidoSalvo->cliente->id)->toBe($this->pedido->cliente->id);
            expect($pedidoSalvo->status)->toBe(StatusPedido::RECEBIDO);
            expect($pedidoSalvo->taxaEntrega->centavos)->toBe(1000);
            expect($pedidoSalvo->itensPedido)->toHaveLength(2);
            expect($pedidoSalvo->descontosPromocao)->toHaveLength(1);

            $itemPizza = $pedidoSalvo->itensPedido[0];
            $saborBacon = $itemPizza->saboresEscolhidos[0];
            $saborPortuguesa = $itemPizza->saboresEscolhidos[1];

            expect($itemPizza)->toBeAnInstanceOf(ItemPizza::class);
            expect($itemPizza->quantidade)->toBe(2);
            expect($itemPizza->tamanhoPizza)->toBe(TamanhoPizza::GRANDE);
            expect($itemPizza->precoPraticado->centavos)->toBe(5750);
            expect($itemPizza->saboresEscolhidos)->toHaveLength(2);

            expect($saborBacon)->toBeAnInstanceOf(SaborEscolhido::class);
            expect($saborBacon->pizza->id)->toBe($this->pizzaBacon->id);
            expect($saborBacon->ingredientesEscolhidos)->toHaveLength(4);

            expect($saborPortuguesa)->toBeAnInstanceOf(SaborEscolhido::class);
            expect($saborPortuguesa->pizza->id)->toBe($this->pizzaPortuguesa->id);
            expect($saborPortuguesa->ingredientesEscolhidos)->toHaveLength(6);

            $descontoPromocao = $pedidoSalvo->descontosPromocao[0];
            expect($descontoPromocao)->toBeAnInstanceOf(DescontoPromocao::class);
            expect($descontoPromocao->promocao->id)->toBe($this->promocao->id);
            expect($descontoPromocao->valorDesconto->centavos)->toBe(1150);

            $descontoFidelidade = $pedidoSalvo->descontoFidelidade;
            expect($descontoFidelidade)->toBeAnInstanceOf(DescontoFidelidade::class);
            expect($descontoFidelidade->beneficioFidelidade->id)->toBe($this->cliente->beneficioFidelidade->id);
            expect($descontoFidelidade->valorDesconto->centavos)->toBe(1185);
        });

        it('lança exceção quando ocorre um erro na persistência', function () {
            expect(function () {
                $this->repositorioPedido->registrarPedido($this->pedidoErro);
            })->toThrow(new RepositorioException('Erro ao registrar pedido.'));
        });
    });

    describe('registrarCancelamento()', function () {

        it('persiste o motivo e a data do cancelamento do pedido', function () {
            $idPedido = (int) $this->bancoDados->consultar('SELECT id FROM pedido ORDER BY id DESC LIMIT 1')[0]['id'];

            $this->repositorioPedido->registrarCancelamento($idPedido, new Cancelamento(
                id: 0,
                motivo: 'Cliente desistiu do pedido',
                data: new DateTime('2026-10-28 20:00:00'),
            ));

            $dadosCancelamento = $this->bancoDados->consultar(
                'SELECT motivo, data FROM cancelamento WHERE idPedido = :pedidoId',
                ['pedidoId' => $idPedido],
            );
            expect($dadosCancelamento)->toBe([
                ['motivo' => 'Cliente desistiu do pedido', 'data' => '2026-10-28 20:00:00'],
            ]);
        });

    });

    describe('obterPedidoPorId()', function () {

        it('retorna o pedido com cliente, itens e entrega registrados', function () {
            $pizza = $this->repositorioPizza->obterPizzaPorId(1);
            $cliente = $this->repositorioCliente->obterClientePorId('1');
            $entregador = $this->repositorioEntregador->obterEntregadorPorId(1);

            $pedido = new Pedido(
                id: 0,
                usuario: UsuarioFactory::atendente(),
                data: new DateTime(),
                tipoAtendimento: TipoAtendimento::ENTREGA,
                cliente: $cliente,
                itensPedido: [
                    new ItemPizza(
                        id: 0,
                        quantidade: 1,
                        saboresEscolhidos: [
                            new SaborEscolhido(
                                id: 0,
                                pizza: $pizza,
                                ingredientesEscolhidos: array_map(
                                    fn(IngredientePadrao $ingredientePadrao): IngredienteEscolhido => IngredienteEscolhidoFactory::padrao(0, $ingredientePadrao->ingrediente),
                                    $pizza->ingredientesPadrao,
                                ),
                            ),
                        ],
                        tamanhoPizza: $pizza->precosPorTamanho[0]->tamanhoPizza,
                    ),
                ],
                status: StatusPedido::EM_PREPARACAO,
            );
            $this->repositorioPedido->registrarPedido($pedido);
            $this->repositorioPedido->registrarEntrega($pedido, new Entrega(
                id: 0,
                endereco: $cliente->endereco,
                entregador: $entregador,
                dataSaida: new DateTime('2026-10-28 20:00:00'),
                dataChegada: null,
                taxa: $pedido->calcularValorEntrega(),
            ));

            $pedidoObtido = $this->repositorioPedido->obterPedidoPorId((string) $pedido->id);

            expect($pedidoObtido)->toBeAnInstanceOf(Pedido::class);
            expect($pedidoObtido->tipoAtendimento)->toBe(TipoAtendimento::ENTREGA);
            expect($pedidoObtido->cliente->id)->toBe(1);
            expect($pedidoObtido->itensPedido[0]->saboresEscolhidos[0]->pizza->id)->toBe(1);
            expect($pedidoObtido->calcularValorTotal()->centavos)->toBe($pedido->calcularValorTotal()->centavos);
            expect($pedidoObtido->entrega->entregador->id)->toBe(1);
            expect($pedidoObtido->entrega->dataSaida->format('Y-m-d H:i:s'))->toBe('2026-10-28 20:00:00');
        });

    });
});
