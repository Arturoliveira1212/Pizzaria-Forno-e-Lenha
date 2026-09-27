<?php

describe('FiltroPizza', function () {

    describe('validar()', function () {

        $this->paginacaoValida = new Paginacao();

        it('cria o filtro quando o identificador é vazio, representando "sem filtro" (todas as pizzas)', function () {
            $filtro = new FiltroPizza(identificador: '', paginacao: $this->paginacaoValida);

            expect($filtro->identificador)->toBe('');
        });

        it('lança exceção quando o identificador tem mais caracteres que o máximo permitido', function () {
            expect(function () {
                $identificador = str_repeat('a', FiltroPizza::QUANTIDADE_MAXIMA_DE_CARACTERES_PARA_BUSCA + 1);
                new FiltroPizza(identificador: $identificador, paginacao: $this->paginacaoValida);
            })->toThrow(new DominioException('O identificador deve ter no máximo ' . FiltroPizza::QUANTIDADE_MAXIMA_DE_CARACTERES_PARA_BUSCA . ' caracteres.'));
        });

        it('cria o filtro quando todos os campos são válidos', function () {
            $identificador = str_repeat('a', FiltroPizza::QUANTIDADE_MAXIMA_DE_CARACTERES_PARA_BUSCA);
            $filtro = new FiltroPizza(identificador: $identificador, paginacao: $this->paginacaoValida);

            expect($filtro->identificador)->toBe($identificador);
            expect($filtro->paginacao)->toBe($this->paginacaoValida);
        });

    });

});
