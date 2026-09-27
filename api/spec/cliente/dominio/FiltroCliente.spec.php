<?php

describe('FiltroCliente', function () {

    describe('validar()', function () {

        $this->paginacaoValida = new Paginacao();

        it('cria o filtro quando o identificador é vazio, representando "sem filtro" (todos os clientes)', function () {
            $filtro = new FiltroCliente(identificador: '', paginacao: $this->paginacaoValida);

            expect($filtro->identificador)->toBe('');
        });

        it('lança exceção quando o identificador tem menos caracteres que o mínimo permitido', function () {
            expect(function () {
                $identificador = str_repeat('a', FiltroCliente::QUANTIDADE_MINIMA_DE_CARACTERES_PARA_BUSCA - 1);
                new FiltroCliente(identificador: $identificador, paginacao: $this->paginacaoValida);
            })->toThrow(new DominioException('O identificador deve ter entre ' . FiltroCliente::QUANTIDADE_MINIMA_DE_CARACTERES_PARA_BUSCA . ' e ' . FiltroCliente::QUANTIDADE_MAXIMA_DE_CARACTERES_PARA_BUSCA . ' caracteres.'));
        });

        it('lança exceção quando o identificador tem mais caracteres que o máximo permitido', function () {
            expect(function () {
                $identificador = str_repeat('a', FiltroCliente::QUANTIDADE_MAXIMA_DE_CARACTERES_PARA_BUSCA + 1);
                new FiltroCliente(identificador: $identificador, paginacao: $this->paginacaoValida);
            })->toThrow(new DominioException('O identificador deve ter entre ' . FiltroCliente::QUANTIDADE_MINIMA_DE_CARACTERES_PARA_BUSCA . ' e ' . FiltroCliente::QUANTIDADE_MAXIMA_DE_CARACTERES_PARA_BUSCA . ' caracteres.'));
        });

        it('cria o filtro quando todos os campos são válidos', function () {
            $identificador = str_repeat('a', FiltroCliente::QUANTIDADE_MINIMA_DE_CARACTERES_PARA_BUSCA);
            $filtro = new FiltroCliente(identificador: $identificador, paginacao: $this->paginacaoValida);

            expect($filtro->identificador)->toBe($identificador);
            expect($filtro->paginacao)->toBe($this->paginacaoValida);
        });

    });

});
