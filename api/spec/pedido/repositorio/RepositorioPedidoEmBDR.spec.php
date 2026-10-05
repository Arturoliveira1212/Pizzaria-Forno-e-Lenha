<?php

describe('RepositorioPedidoEmBDR', function () {

    beforeAll(function () {
        $this->bancoDados = new BancoDados(PDOFactory::criar(
            nomeBanco: $_ENV['DB_NAME_TEST'],
            host: $_ENV['DB_HOST_TEST'],
            usuario: $_ENV['DB_USERNAME_TEST'],
            senha: $_ENV['DB_PASSWORD_TEST'],
        ));

        $this->repositorioPedido = new RepositorioPedidoEmBDR($this->bancoDados);
    });

    describe('registrarPedido()', function () {

        beforeAll(function () {
            $molho = new Ingrediente(1, 'Molho de Tomate');
            $mussarela = new Ingrediente(2, 'Mussarela');

            $pizza = new Pizza(
                id: 1,
                nome: 'Margherita',
                descricao: 'Pizza margherita',
                ingredientesPadrao: [
                    new IngredientePadrao(id: 1, ingrediente: $molho, removivel: false, substituivel: false),
                    new IngredientePadrao(id: 2, ingrediente: $mussarela, removivel: false, substituivel: false),
                ],
                ingredientesSubstitutos: [],
                adicionais: [],
                precosPorTamanho: [
                    new PrecoPorTamanho(id: 1, tamanhoPizza: TamanhoPizza::MEDIA, preco: new Dinheiro(4200)),
                ],
            );

            $saborEscolhido = new SaborEscolhido(
                id: 0,
                pizza: $pizza,
                ingredientesEscolhidos: [
                    new IngredienteEscolhido(0, $molho, PapelIngrediente::PADRAO, Dinheiro::zero()),
                    new IngredienteEscolhido(0, $mussarela, PapelIngrediente::PADRAO, Dinheiro::zero()),
                ],
            );

            $itemPizza = new ItemPizza(
                id: 0,
                quantidade: 1,
                saboresEscolhidos: [$saborEscolhido],
                tamanhoPizza: TamanhoPizza::MEDIA,
            );

            $itemSimples = new ItemSimples(
                id: 0,
                quantidade: 1,
                produtoSimples: new ProdutoSimples(
                    1,
                    'Coca-Cola 2L',
                    'Refrigerante',
                    TipoProdutoSimples::BEBIDA,
                    new Dinheiro(1200),
                ),
            );

            $cliente = new Cliente(
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
                data: new DateTime(),
                tipoAtendimento: TipoAtendimento::ENTREGA,
                cliente: $cliente,
                itensPedido: [
                    $itemSimples,
                    $itemPizza,
                ],
                status: StatusPedido::RECEBIDO,
                descontosPromocao: [new DescontoPromocao(
                    0,
                    new Dinheiro(500),
                    new Promocao(
                        1,
                        'Promocao de teste',
                        1,
                        TamanhoPizza::MEDIA,
                        new Percentual(10),
                        null,
                        null,
                    ),
                )],
                descontoFidelidade: new DescontoFidelidade(
                    0,
                    new Dinheiro(490),
                    $cliente->beneficioFidelidade,
                ),
            );

            $this->pedidoErro = new Pedido(
                id: 0,
                data: new DateTime('-1 minute'),
                tipoAtendimento: TipoAtendimento::BALCAO,
                cliente: null,
                itensPedido: [new ItemSimples(0, 1, new ProdutoSimples(999999, 'Inexistente', 'Inexistente', TipoProdutoSimples::BEBIDA, new Dinheiro(100)))],
                status: StatusPedido::RECEBIDO,
            );
        });

        it('persiste o pedido e todas as suas relações', function () {
            $this->repositorioPedido->registrarPedido($this->pedidoSucesso);

            $pedidoPersistido = $this->bancoDados->consultar('SELECT * FROM pedido ORDER BY id DESC LIMIT 1')[0];
            $idPedido = (int) $pedidoPersistido['id'];

            expect($pedidoPersistido['valorTotal'])->toBe(5410);
            expect($pedidoPersistido['taxaEntrega'])->toBe(1000);
            expect($pedidoPersistido['status'])->toBe('RECEBIDO');
            expect($pedidoPersistido['tipoAtendimento'])->toBe('ENTREGA');
            expect($pedidoPersistido['idCliente'])->toBe(11);

            $dadosItemSimples = $this->bancoDados->consultar(
                'SELECT quantidade, precoPraticado, produtoSimplesId FROM item_pedido_simples WHERE pedidoId = :pedidoId',
                ['pedidoId' => $idPedido],
            );
            expect($dadosItemSimples)->toBe([
                ['quantidade' => 1, 'precoPraticado' => 1200, 'produtoSimplesId' => 1],
            ]);

            $dadosItemPizza = $this->bancoDados->consultar(
                'SELECT quantidade, precoPraticado, tamanho FROM item_pedido_pizza WHERE pedidoId = :pedidoId',
                ['pedidoId' => $idPedido],
            );

            expect($dadosItemPizza)->toBe([
                ['quantidade' => 1, 'precoPraticado' => 4200, 'tamanho' => 'MEDIA'],
            ]);

            $dadosSaboresEscolhidos = $this->bancoDados->consultar(
                'SELECT sabor_escolhido.pizzaId
                    FROM sabor_escolhido
                    INNER JOIN item_pedido_pizza ON item_pedido_pizza.id = sabor_escolhido.itemPedidoPizzaId
                        WHERE item_pedido_pizza.pedidoId = :pedidoId',
                ['pedidoId' => $idPedido],
            );
            expect($dadosSaboresEscolhidos)->toBe([
                ['pizzaId' => 1],
            ]);

            $dadosIngredientesEscolhidos = $this->bancoDados->consultar(
                'SELECT ingrediente_escolhido.ingredienteId, ingrediente_escolhido.custo, ingrediente_escolhido.papel
                    FROM ingrediente_escolhido
                    INNER JOIN sabor_escolhido ON sabor_escolhido.id = ingrediente_escolhido.saborEscolhidoId
                    INNER JOIN item_pedido_pizza ON item_pedido_pizza.id = sabor_escolhido.itemPedidoPizzaId
                        WHERE item_pedido_pizza.pedidoId = :pedidoId
                            ORDER BY ingrediente_escolhido.ingredienteId',
                ['pedidoId' => $idPedido],
            );
            expect($dadosIngredientesEscolhidos)->toBe([
                ['ingredienteId' => 1, 'custo' => 0, 'papel' => 'PADRAO'],
                ['ingredienteId' => 2, 'custo' => 0, 'papel' => 'PADRAO'],
            ]);

            $dadosDescontosPromocao = $this->bancoDados->consultar(
                'SELECT valorDesconto, promocaoId FROM desconto_promocao WHERE pedidoId = :pedidoId',
                ['pedidoId' => $idPedido],
            );
            expect($dadosDescontosPromocao)->toBe([
                ['valorDesconto' => 500, 'promocaoId' => 1],
            ]);

            $dadosDescontoFidelidade = $this->bancoDados->consultar(
                'SELECT valorDesconto, beneficioFidelidadeId FROM desconto_fidelidade WHERE pedidoId = :pedidoId',
                ['pedidoId' => $idPedido],
            );
            expect($dadosDescontoFidelidade)->toBe([
                ['valorDesconto' => 490, 'beneficioFidelidadeId' => 1],
            ]);
        });

        it('lança exceção quando ocorre um erro na persistência', function () {
            expect(function () {
                $this->repositorioPedido->registrarPedido($this->pedidoErro);
            })->toThrow(new RepositorioException('Erro ao registrar pedido.'));
        });
    });
});
