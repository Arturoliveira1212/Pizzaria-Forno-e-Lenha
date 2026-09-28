<?php

use Kahlan\Plugin\Double;

describe('GestorPedido', function () {

    beforeEach(function () {
        $this->repositorioPedido = Double::instance(['implements' => RepositorioPedido::class]);
        $this->repositorioCliente = Double::instance(['implements' => RepositorioCliente::class]);
        $this->repositorioPizza = Double::instance(['implements' => RepositorioPizza::class]);
        $this->repositorioProdutoSimples = Double::instance(['implements' => RepositorioProdutoSimples::class]);
        $this->repositorioIngrediente = Double::instance(['implements' => RepositorioIngrediente::class]);
        $this->repositorioPromocao = Double::instance(['implements' => RepositorioPromocao::class]);

        $this->gestorPedido = new GestorPedido(
            repositorioPedido: $this->repositorioPedido,
            repositorioCliente: $this->repositorioCliente,
            repositorioPizza: $this->repositorioPizza,
            repositorioProdutoSimples: $this->repositorioProdutoSimples,
            repositorioIngrediente: $this->repositorioIngrediente,
            repositorioPromocao: $this->repositorioPromocao,
        );
    });

    describe('registrarPedido()', function () {

        $this->pizza = new Pizza(
            id: 1,
            nome: 'Pizza Teste',
            descricao: 'Descrição da pizza teste',
            precosPorTamanho: [],
            ingredientesPadrao: [],
            adicionais: [],
            ingredientesSubstitutos: [],
        );

        describe('TipoAtendimento', function () {

            it('lança exceção quando o tipo atendimento é inválido', function () {
                $tipoQueNaoExiste = 'tipo_que_nao_existe';
                expect(function () use ($tipoQueNaoExiste) {

                    $this->gestorPedido->registrarPedido(
                        new PedidoParaCadastro(
                            tipo: $tipoQueNaoExiste,
                            idCliente: 'dasdsadasdsadas',
                            itensPizza: [],
                            itensSimples: [],
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
                            itensPizza: [
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
                            itensSimples: [],
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
                            itensPizza: [
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
                            itensSimples: [],
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
                            itensPizza: [
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
                            itensSimples: [],
                        ),
                    );
                })->toThrow(new DominioException("Ingrediente (ID {$idInexistente}) não encontrado."));

            });

            it('lança exceção quando o papel do ingrediente é inválido', function () {
                allow($this->repositorioPizza)->toReceive('obterPizzaPorId')->andReturn($this->pizza);
                allow($this->repositorioIngrediente)->toReceive('obterIngredientePorId')->andReturn(new Ingrediente(1, 'Queijo'));

                $papelQueNaoExiste = 'papel_que_nao_existe';

                expect(function () use ($papelQueNaoExiste) {
                    $this->gestorPedido->registrarPedido(
                        new PedidoParaCadastro(
                            tipo: TipoAtendimento::BALCAO->value,
                            idCliente: 'dasdsadasdsadas',
                            itensPizza: [
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
                            itensSimples: [],
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
                            itensPizza: [],
                            itensSimples: [
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
            $this->promocoesVigentes = [
                new Promocao(
                    id: 1,
                    descricao: 'Promoção Teste',
                    quantidadeMinima: 1,
                    tamanhoPizza: TamanhoPizza::MEDIA,
                    percentualDesconto: new Percentual(10),
                    dataInicio: new DateTime('2024-01-01'),
                    dataFim: new DateTime('2027-12-31'),
                ),
            ];

            // it('calcula corretamente os descontos de promoção', function () {
            //     allow($this->repositorioPromocao)->toReceive('obterPromocoes')->andReturn([]);

            //     expect(function () {
            //         $this->gestorPedido->registrarPedido(
            //             new PedidoParaCadastro(
            //                 tipo: TipoAtendimento::BALCAO->value,
            //                 idCliente: 'dasdsadasdsadas',
            //                 itensPizza: [
            //                     new ItemPizzaParaCadastro(
            //                         tamanho: TamanhoPizza::MEDIA->value,
            //                         quantidade: 1,
            //                         saboresEscolhidos: [
            //                             new SaborEscolhidoParaCadastro(
            //                                 idPizza: 1,
            //                                 ingredientesEscolhidos: [],
            //                             ),
            //                         ],
            //                     ),
            //                 ],
            //                 itensSimples: [],
            //             ),
            //         );
            //     })->toThrow(new DominioException('Nenhuma promoção vigente encontrada.'));
            // });

        });

    });

});
