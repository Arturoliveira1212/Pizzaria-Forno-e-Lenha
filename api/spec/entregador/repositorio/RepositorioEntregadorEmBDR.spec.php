<?php

describe('RepositorioEntregadorEmBDR', function () {

    beforeAll(function () {
        $this->bancoDados = new BancoDados(PDOFactory::criar(
            nomeBanco: $_ENV['DB_NAME_TEST'],
            host: $_ENV['DB_HOST_TEST'],
            usuario: $_ENV['DB_USERNAME_TEST'],
            senha: $_ENV['DB_PASSWORD_TEST'],
        ));

        $this->repositorioEntregador = new RepositorioEntregadorEmBDR($this->bancoDados);
    });

    describe('obterEntregadoresDisponiveis()', function () {

        it('retorna apenas os entregadores disponíveis ordenados pelo nome', function () {
            $entregadores = $this->repositorioEntregador->obterEntregadoresDisponiveis();

            $nomes = array_map(fn(Entregador $entregador): string => $entregador->nome, $entregadores);

            expect($nomes)->toBe(['João Pedro Alves', 'Marcos Vinícius Rocha']);
        });

    });

    describe('obterEntregadorPorId()', function () {

        it('retorna null quando o entregador não existe', function () {
            expect($this->repositorioEntregador->obterEntregadorPorId(999))->toBeNull();
        });

        it('retorna o entregador com telefone e disponibilidade quando ele existe', function () {
            $entregador = $this->repositorioEntregador->obterEntregadorPorId(2);

            expect($entregador)->toBeAnInstanceOf(Entregador::class);
            expect($entregador->id)->toBe(2);
            expect($entregador->nome)->toBe('Marcos Vinícius Rocha');
            expect($entregador->telefone->formatar())->toBe('(21) 976543210');
            expect($entregador->disponivel)->toBe(true);
        });

        it('retorna o entregador indisponível quando ele existe', function () {
            $entregador = $this->repositorioEntregador->obterEntregadorPorId(3);

            expect($entregador->nome)->toBe('Juliana Ferreira Dias');
            expect($entregador->disponivel)->toBe(false);
        });

    });

});
