<?php

describe('RepositorioProdutoSimplesEmBDR', function () {

    beforeAll(function () {
        $this->bancoDados = new BancoDados(PDOFactory::criar(
            nomeBanco: $_ENV['DB_NAME_TEST'],
            host: $_ENV['DB_HOST_TEST'],
            usuario: $_ENV['DB_USERNAME_TEST'],
            senha: $_ENV['DB_PASSWORD_TEST'],
        ));

        $this->repositorioProdutoSimples = new RepositorioProdutoSimplesEmBDR($this->bancoDados);
    });

    describe('atualizarEstoqueProdutoSimples()', function () {

        it('lança exceção ao adicionar estoque em um produto simples inexistente', function () {
            expect(function () {
                $this->repositorioProdutoSimples->atualizarEstoqueProdutoSimples(
                    idProduto: 999,
                    quantidade: new QuantidadeMedida(5, UnidadeMedidaFactory::unidade()),
                    tipoOperacaoEstoque: TipoOperacaoEstoque::ENTRADA,
                );
            })->toThrow(new RepositorioException('Erro ao atualizar o estoque do produto.'));
        });

        it('adiciona estoque ao produto simples Petit Gateau corretamente', function () {
            $idProduto = 4;
            $quantidade = new QuantidadeMedida(10, UnidadeMedidaFactory::unidade());

            $this->repositorioProdutoSimples->atualizarEstoqueProdutoSimples(
                idProduto: $idProduto,
                quantidade: $quantidade,
                tipoOperacaoEstoque: TipoOperacaoEstoque::ENTRADA,
            );

            $produtoAtualizado = $this->repositorioProdutoSimples->obterProdutoSimplesPorId($idProduto);

            expect($produtoAtualizado)->toBeAnInstanceOf(ProdutoSimples::class);
            expect($produtoAtualizado->id)->toBe($idProduto);
            expect($produtoAtualizado->estoque->valor)->toBe(25);
        });

        it('subtrai estoque do produto simples Petit Gateau corretamente', function () {
            $idProduto = 4;
            $quantidade = new QuantidadeMedida(7, UnidadeMedidaFactory::unidade());

            $this->repositorioProdutoSimples->atualizarEstoqueProdutoSimples(
                idProduto: $idProduto,
                quantidade: $quantidade,
                tipoOperacaoEstoque: TipoOperacaoEstoque::SAIDA,
            );

            $produtoAtualizado = $this->repositorioProdutoSimples->obterProdutoSimplesPorId($idProduto);

            expect($produtoAtualizado)->toBeAnInstanceOf(ProdutoSimples::class);
            expect($produtoAtualizado->id)->toBe($idProduto);
            expect($produtoAtualizado->estoque->valor)->toBe(18);
        });

        it('lança exceção ao subtrair uma quantidade maior que o estoque disponível do produto Petit Gateau', function () {
            $idProduto = 4;
            $quantidade = new QuantidadeMedida(19, UnidadeMedidaFactory::unidade());

            expect(function () use ($idProduto, $quantidade) {
                $this->repositorioProdutoSimples->atualizarEstoqueProdutoSimples(
                    idProduto: $idProduto,
                    quantidade: $quantidade,
                    tipoOperacaoEstoque: TipoOperacaoEstoque::SAIDA,
                );
            })->toThrow(new RepositorioException('Erro ao atualizar o estoque do produto.'));
        });
    });

});
