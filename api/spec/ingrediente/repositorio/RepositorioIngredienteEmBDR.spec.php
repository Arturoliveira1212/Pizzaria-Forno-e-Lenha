<?php

describe('RepositorioIngredienteEmBDR', function () {

    beforeAll(function () {
        $this->bancoDados = new BancoDados(PDOFactory::criar(
            nomeBanco: $_ENV['DB_NAME_TEST'],
            host: $_ENV['DB_HOST_TEST'],
            usuario: $_ENV['DB_USERNAME_TEST'],
            senha: $_ENV['DB_PASSWORD_TEST'],
        ));

        $this->repositorioIngrediente = new RepositorioIngredienteEmBDR($this->bancoDados);
    });

    describe('atualizarEstoqueIngrediente()', function () {

        it('lança exceção ao adicionar estoque em um ingrediente inexistente', function () {
            expect(function () {
                $this->repositorioIngrediente->atualizarEstoqueIngrediente(
                    idIngrediente: 999,
                    quantidade: new QuantidadeMedida(5, UnidadeMedidaFactory::gramas()),
                    tipoOperacaoEstoque: TipoOperacaoEstoque::ENTRADA,
                );
            })->toThrow(new RepositorioException('Erro ao atualizar o estoque do ingrediente.'));
        });

        it('adiciona estoque ao ingrediente orégano corretamente', function () {
            $idIngrediente = 19;
            $quantidade = new QuantidadeMedida(200, UnidadeMedidaFactory::gramas());

            $this->repositorioIngrediente->atualizarEstoqueIngrediente(
                idIngrediente: $idIngrediente,
                quantidade: $quantidade,
                tipoOperacaoEstoque: TipoOperacaoEstoque::ENTRADA,
            );

            $ingredienteAtualizado = $this->repositorioIngrediente->obterIngredientePorId($idIngrediente);

            expect($ingredienteAtualizado)->toBeAnInstanceOf(Ingrediente::class);
            expect($ingredienteAtualizado->id)->toBe($idIngrediente);
            expect($ingredienteAtualizado->estoque->valor)->toBe(1180);
        });

        it('subtrai estoque do ingrediente orégano corretamente', function () {
            $idIngrediente = 19;
            $quantidade = new QuantidadeMedida(500, UnidadeMedidaFactory::gramas());

            $this->repositorioIngrediente->atualizarEstoqueIngrediente(
                idIngrediente: $idIngrediente,
                quantidade: $quantidade,
                tipoOperacaoEstoque: TipoOperacaoEstoque::SAIDA,
            );

            $ingredienteAtualizado = $this->repositorioIngrediente->obterIngredientePorId($idIngrediente);

            expect($ingredienteAtualizado)->toBeAnInstanceOf(Ingrediente::class);
            expect($ingredienteAtualizado->id)->toBe($idIngrediente);
            expect($ingredienteAtualizado->estoque->valor)->toBe(680);
        });

        it('lança exceção ao subtrair uma quantidade maior que o estoque disponível do ingrediente orégano', function () {
            expect(function () {
                $this->repositorioIngrediente->atualizarEstoqueIngrediente(
                    idIngrediente: 19,
                    quantidade: new QuantidadeMedida(681, UnidadeMedidaFactory::gramas()),
                    tipoOperacaoEstoque: TipoOperacaoEstoque::SAIDA,
                );
            })->toThrow(new RepositorioException('Erro ao atualizar o estoque do ingrediente.'));
        });
    });

});
