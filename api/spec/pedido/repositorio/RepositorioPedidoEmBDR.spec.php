<?php

describe('RepositorioPedidoEmBDR', function () {

    beforeAll(function () {
        $this->bancoDados = new BancoDados(PDOFactory::criar(
            nomeBanco: $_ENV['DB_NAME_TEST'],
            host: $_ENV['DB_HOST_TEST'],
            usuario: $_ENV['DB_USERNAME_TEST'],
            senha: $_ENV['DB_PASSWORD_TEST'],
        ));

        $this->repositorioPedido = new RepositorioPedidoEmBDR(
            bancoDados: $this->bancoDados,
            repositorioPizza: new RepositorioPizzaEmBDR($this->bancoDados),
            repositorioCliente: new RepositorioClienteEmBDR($this->bancoDados),
            repositorioUsuario: new RepositorioUsuarioEmBDR(),
        );
    });

    describe('registrarPedido()', function () {

        beforeAll(function () {
            $this->itemPizza = new ItemPizza(
                id: 0,
                quantidade: 1,
                saboresEscolhidos: [
                    new SaborEscolhido(
                        id: 0,
                        pizza: PizzaFactory::margherita(),
                        ingredientesEscolhidos: [
                            IngredienteEscolhidoFactory::padrao(1, IngredienteFactory::molho()),
                            IngredienteEscolhidoFactory::padrao(2, IngredienteFactory::mussarela()),
                        ],
                    ),
                ],
                tamanhoPizza: TamanhoPizza::MEDIA,
            );

            $this->itemSimples = new ItemSimples(
                id: 0,
                quantidade: 1,
                produtoSimples: ProdutoSimplesFactory::cocaCola(),
            );

            $this->cliente = new Cliente(
                id: 11,
                nome: 'Artur Alves',
                telefone: new Telefone('22', '99999-9999'),
                cpf: new Cpf('15975348625'),
                endereco: new Endereco(
                    id: 0,
                    logradouro: 'Rua A',
                    numero: '1',
                    bairro: 'Centro',
                    cidade: 'Cidade',
                    estado: 'Rio de Janeiro',
                    uf: 'RJ',
                ),
                beneficioFidelidade: new BeneficioFidelidade(
                    id: 1,
                    percentualDesconto: new Percentual(5),
                    dataConcessao: new DateTime('-1 day'),
                    validade: new DateTime('+7 days'),
                    utilizado: false,
                ),
            );

            $this->pedidoSucesso = new Pedido(
                id: 0,
                usuario: UsuarioFactory::atendente(),
                data: new DateTime(),
                tipoAtendimento: TipoAtendimento::ENTREGA,
                cliente: $this->cliente,
                itensPedido: [
                    $this->itemSimples,
                    $this->itemPizza,
                ],
                status: StatusPedido::RECEBIDO,
                descontosPromocao: [
                    new DescontoPromocao(
                        1,
                        new Dinheiro(309),
                        new Promocao(
                            1,
                            'Promocao de teste',
                            1,
                            TamanhoPizza::MEDIA,
                            new Percentual(10),
                            null,
                            null,
                        ),
                    ),
                ],
                descontoFidelidade: new DescontoFidelidade(
                    1,
                    new Dinheiro(337),
                    $this->cliente->beneficioFidelidade,
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

        it('persiste o pedido e todas as suas relações', function () {
            $this->repositorioPedido->registrarPedido($this->pedidoSucesso);

            $pedidoPersistido = $this->bancoDados->consultar('SELECT * FROM pedido ORDER BY id DESC LIMIT 1')[0];
            $idPedido = (int) $pedidoPersistido['id'];

            expect($pedidoPersistido['valorTotal'])->toBe(4034);
            expect($pedidoPersistido['taxaEntrega'])->toBe(1000);
            expect($pedidoPersistido['status'])->toBe('RECEBIDO');
            expect($pedidoPersistido['tipoAtendimento'])->toBe('ENTREGA');
            expect($pedidoPersistido['idCliente'])->toBe(11);

            $dadosItemSimples = $this->bancoDados->consultar(
                'SELECT quantidade, precoPraticado, idProdutoSimples FROM item_pedido_simples WHERE idPedido = :pedidoId',
                ['pedidoId' => $idPedido],
            );
            expect($dadosItemSimples)->toBe([
                ['quantidade' => 1, 'precoPraticado' => 590, 'idProdutoSimples' => 1],
            ]);

            $dadosItemPizza = $this->bancoDados->consultar(
                'SELECT quantidade, precoPraticado, tamanho FROM item_pedido_pizza WHERE idPedido = :pedidoId',
                ['pedidoId' => $idPedido],
            );

            expect($dadosItemPizza)->toBe([
                ['quantidade' => 1, 'precoPraticado' => 3090, 'tamanho' => 'MEDIA'],
            ]);

            $dadosSaboresEscolhidos = $this->bancoDados->consultar(
                'SELECT sabor_escolhido.idPizza
                    FROM sabor_escolhido
                    INNER JOIN item_pedido_pizza ON item_pedido_pizza.id = sabor_escolhido.idItemPedidoPizza
                        WHERE item_pedido_pizza.idPedido = :pedidoId',
                ['pedidoId' => $idPedido],
            );
            expect($dadosSaboresEscolhidos)->toBe([
                ['idPizza' => 2],
            ]);

            $dadosIngredientesEscolhidos = $this->bancoDados->consultar(
                'SELECT ingrediente_escolhido.idIngrediente, ingrediente_escolhido.custo, ingrediente_escolhido.papel
                    FROM ingrediente_escolhido
                    INNER JOIN sabor_escolhido ON sabor_escolhido.id = ingrediente_escolhido.idSaborEscolhido
                    INNER JOIN item_pedido_pizza ON item_pedido_pizza.id = sabor_escolhido.idItemPedidoPizza
                        WHERE item_pedido_pizza.idPedido = :pedidoId
                            ORDER BY ingrediente_escolhido.idIngrediente',
                ['pedidoId' => $idPedido],
            );
            expect($dadosIngredientesEscolhidos)->toBe([
                ['idIngrediente' => 1, 'custo' => 0, 'papel' => 'PADRAO'],
                ['idIngrediente' => 2, 'custo' => 0, 'papel' => 'PADRAO'],
            ]);

            $dadosDescontosPromocao = $this->bancoDados->consultar(
                'SELECT valorDesconto, idPromocao FROM desconto_promocao WHERE idPedido = :pedidoId',
                ['pedidoId' => $idPedido],
            );
            expect($dadosDescontosPromocao)->toBe([
                ['valorDesconto' => 309, 'idPromocao' => 1],
            ]);

            $dadosDescontoFidelidade = $this->bancoDados->consultar(
                'SELECT valorDesconto, idBeneficioFidelidade FROM desconto_fidelidade WHERE idPedido = :pedidoId',
                ['pedidoId' => $idPedido],
            );
            expect($dadosDescontoFidelidade)->toBe([
                ['valorDesconto' => 337, 'idBeneficioFidelidade' => 1],
            ]);
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
});
